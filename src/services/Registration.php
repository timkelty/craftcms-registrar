<?php
namespace timkelty\craftcms\registrar\services;

use Craft;
use craft\events\ModelEvent;
use timkelty\craftcms\registrar\Plugin;
use yii\base\Component;
use yii\base\DynamicModel;

class Registration extends Component
{
  private $_validatedTests = [];

  public function beforeUserSave(ModelEvent $event)
  {
    if (!$this->isPublicRegistration($event)) {
      return;
    }

    $user = $event->sender;
    $settings = Plugin::getInstance()->getSettings();

    // Validate settings before we use them
    $settings->validate();

    $this->_validatedTests = array_filter($settings->tests, function ($test) use ($user) {
      $model = new DynamicModel([
        $test->attribute => $user->{$test->attribute}
      ]);

      $model->addRule($test->attribute, $test->validator, $test->options);

      if ($model->validate()) {
        return true;
      }

      $user->addErrors($model->getErrors());
    });

    if (empty($this->_validatedTests) && $settings->requireValidatedTest) {
      $event->isValid = false;
    }
  }

  public function afterUserSave(ModelEvent $event)
  {
    if (!$this->isPublicRegistration($event) || !$this->_validatedTests) {
      return;
    }

    $user = $event->sender;

    foreach ($this->_validatedTests as $test) {
      if (is_callable($test->user)) {
        call_user_func($test->user, $user);
      } elseif ($test->user) {
        Craft::configure($user, $test->user);
      }

      if ($test->groupIds) {
        Craft::$app->getUsers()->assignUserToGroups($user->id, $test->groupIds);
      }

      if ($test->permissions) {
        Craft::$app->getUserPermissions()->saveUserPermissions($user->id, $test->permissions);
      }
    }
  }

  private function isPublicRegistration(ModelEvent $event)
  {
    return $event->isNew && !Craft::$app->getUser()->getIdentity();
  }
}

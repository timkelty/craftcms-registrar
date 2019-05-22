<?php
namespace timkelty\craftcms\registrar\services;

use timkelty\craftcms\registrar\Plugin;
use craft\events\ModelEvent;
use Craft;
use yii\base\Component;
use yii\base\DynamicModel;

class Registration extends Component
{
  private $passedTests = [];

  public function beforeUserSave(ModelEvent $event)
  {
    if (!$event->isNew) {
      return;
    }

    $user = $event->sender;
    $settings = Plugin::getInstance()->getSettings();

    $this->passedTests = array_filter($settings->tests, function ($test) use ($user) {
      $model = new DynamicModel([
        $test->attribute => $user->{$test->attribute}
      ]);

      $model->addRule($test->attribute, $test->validator, $test->options);

      if ($model->validate()) {
        return true;
      }

      $user->addErrors($model->getErrors());
    });

    if (empty($this->passedTests) && $settings->requireValidation) {
      $event->isValid = false;
    }
  }

  public function afterUserSave(ModelEvent $event)
  {
    if (!$event->isNew) {
      return;
    }

    $user = $event->sender;

    foreach ($this->passedTests as $test) {
      if (is_callable($test->user)) {
        call_user_func($test->user, $user);
      } elseif ($user) {
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
}

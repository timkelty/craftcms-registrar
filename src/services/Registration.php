<?php
namespace timkelty\craftcms\registrar\services;

use Craft;
use craft\events\ModelEvent;
use timkelty\craftcms\registrar\Plugin;
use timkelty\craftcms\registrar\events\RegistrationTestEvent;
use yii\base\Component;
use yii\base\DynamicModel;

class Registration extends Component
{
  const EVENT_BEFORE_VALIDATE_TEST = 'beforeValidateTest';

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
      $testEvent = new RegistrationTestEvent([
        'user' => $user
      ]);

      ModelEvent::trigger($test, self::EVENT_BEFORE_VALIDATE_TEST, $testEvent);

      if (!$testEvent->isValid) {
        return false;
      }

      // TODO: should this happen in RegistrationTest::getValue?
      $value = $test->value ?? $user->{$test->attribute} ?? null;

      if (!$value) {
        Plugin::error(Plugin::t('{testClass}::attribute must be an attribute of {userClass}, or have {testClass}::value set.', [
          'testClass' => get_class($test),
          'userClass' => get_class($user),
        ]), __METHOD__);

        return false;
      }

      $model = new DynamicModel([
        $test->attribute => $value,
      ]);

      $model->addRule($test->attribute, $test->validator, $test->options);

      if ($model->validate()) {
        return true;
      }

      $user->addErrors($model->getErrors());
    });

    if (empty($this->_validatedTests) && $settings->requireValidatedTest) {
      $event->isValid = false;

      return null;
    }

    foreach ($this->_validatedTests as $test) {
      if (is_callable($test->user)) {
        call_user_func($test->user, $user);
      } elseif ($test->user) {
        Craft::configure($user, $test->user);
      }
    }
  }

  /**
   * Permissions and groups must be set after saving
   */
  public function afterUserSave(ModelEvent $event)
  {
    if (!$this->isPublicRegistration($event) || !$this->_validatedTests) {
      return;
    }

    $user = $event->sender;

    foreach ($this->_validatedTests as $test) {
      if ($test->groupIds) {
        Craft::$app->getUsers()->assignUserToGroups($user->id, $test->groupIds);
      }

      if (!$user->admin && $test->permissions) {
        Craft::$app->getUserPermissions()->saveUserPermissions($user->id, $test->permissions);
      }
    }
  }

  private function isPublicRegistration(ModelEvent $event)
  {
    return $event->isNew && !Craft::$app->getUser()->getIdentity();
  }
}

<?php
namespace timkelty\craftcms\registrar\services;

use Craft;
use craft\events\ModelEvent;
use craft\elements\User;
use timkelty\craftcms\registrar\Plugin;
use timkelty\craftcms\registrar\events\RegistrationTestEvent;
use timkelty\craftcms\registrar\models\RegistrationTest;
use yii\base\Component;
use yii\base\DynamicModel;

class Registration extends Component
{
  const EVENT_BEFORE_VALIDATE_TEST = 'beforeValidateTest';

  private $_validatedTests = [];

  public function beforeUserSave(ModelEvent $event)
  {
    if (!$this->_isPublicRegistration($event)) {
      return;
    }

    $user = $event->sender;
    $settings = Plugin::getInstance()->getSettings();

    // Validate settings before we use them
    $settings->validate();

    $this->_validatedTests = array_filter($settings->tests, function ($test) use ($user) {
      return $this->_validateTest($test, $user);
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
    if (!$this->_isPublicRegistration($event) || empty($this->_validatedTests)) {
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

  private function _isPublicRegistration(ModelEvent $event)
  {
    return $event->isNew && !Craft::$app->getUser()->getIdentity();
  }

  private function _validateTest(RegistrationTest $test, User $user): bool
  {
    $beforeValidateEvent = new RegistrationTestEvent([
      'user' => $user
    ]);

    ModelEvent::trigger($test, self::EVENT_BEFORE_VALIDATE_TEST, $beforeValidateEvent);

    if (!$beforeValidateEvent->isValid) {
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

    if (!$model->validate()) {
      $user->addErrors($model->getErrors());

      return false;
    }

    return true;
  }
}

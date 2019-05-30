<?php
namespace timkelty\craftcms\registrar\services;

use Craft;
use craft\events\ModelEvent;
use craft\elements\User;
use timkelty\craftcms\registrar\Plugin;
use timkelty\craftcms\registrar\events\RegisterTestsEvent;
use timkelty\craftcms\registrar\models\RegistrationTest;
use yii\base\Component;
use yii\base\DynamicModel;
use yii\web\ForbiddenHttpException;

class Registration extends Component
{
  const EVENT_REGISTER_TESTS = 'registerTests';

  private $_validatedTests = [];

  /**
   * Validate tests and configure user
   */
  public function beforeUserSave(ModelEvent $event)
  {
    if (!$this->_isPublicRegistration($event)) {
      return;
    }

    $user = $event->sender;
    $settings = Plugin::getInstance()->getSettings();

    // Validate settings before we use them
    $settings->validate();

    // Add tests via event
    $registerTestsEvent = new RegisterTestsEvent([
      'user' => $user,
      'tests' => $settings->tests,
    ]);

    $this->trigger(self::EVENT_REGISTER_TESTS, $registerTestsEvent);

    // Fallback exception in case we don't end up with any tests
    if (empty($registerTestsEvent->tests)) {
      throw new ForbiddenHttpException('Public registration is not allowed');
    } else {
      $this->_validatedTests = array_filter($registerTestsEvent->tests, function ($test) use ($user) {
        return $this->_validateTest($test, $user);
      });
    }

    if (empty($this->_validatedTests) && $settings->requireValidatedTest) {
      $event->isValid = false;

      return null;
    }

    foreach ($this->_validatedTests as $test) {
      if ($test->user) {
        try {
          Craft::configure($user, $test->user);
        } catch (UnknownPropertyException $e) {
          Plugin::error($e->getMessage(), __METHOD__, UnknownPropertyException::class);
        }
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
    $model = new DynamicModel([
      $test->attribute => $test->value ?? $user->{$test->attribute} ?? null,
    ]);

    $model->addRule($test->attribute, $test->validator, $test->options);

    if (!$model->validate()) {
      $user->addErrors($model->getErrors());

      return false;
    }

    return true;
  }
}

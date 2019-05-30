<?php
namespace timkelty\craftcms\registrar;

use Craft;
use craft\elements\User;
use yii\base\ErrorException;
use yii\base\Event;

class Plugin extends \craft\base\Plugin
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!Craft::$app->getProjectConfig()->get('users.allowPublicRegistration')) {
            self::error('Registrar requires public registration to be allowed.', __METHOD__);

            return;
        }

        if ($this->getSettings()->debug) {
            $this->getSettings()->validate();
        }

        $this->setComponents([
            'registration' => services\Registration::class,
        ]);

        Event::on(
            User::class,
            User::EVENT_BEFORE_SAVE,
            [$this->registration, 'beforeUserSave']
        );

        Event::on(
            User::class,
            User::EVENT_AFTER_SAVE,
            [$this->registration, 'afterUserSave']
        );
    }

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new models\Settings();
    }

    public static function t($message, ...$args)
    {
        return Craft::t(self::getInstance()->handle, $message, ...$args);
    }

    public static function error(string $message, string $category, ?string $throw = null)
    {
        if ($throw === null && self::getInstance()->getSettings()->debug) {
            $throw = ErrorException::class;
        }

        Craft::error($message, $category);

        if ($throw) {
            throw new $throw($message);
        }
    }
}

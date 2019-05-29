<?php
namespace timkelty\craftcms\registrar;

use Craft;
use craft\elements\User;
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
            Craft::error('Registrar requires public registration to be allowed.', __METHOD__);

            return;
        }

        // TODO: we can't bail here because we don't want invalid props to halt everyyhting
        if (!$this->getSettings()->validate()) {
            Craft::error('Invalid plugin configuration.', __METHOD__);

            return;
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
}

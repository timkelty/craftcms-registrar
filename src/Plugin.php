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

        // If settings are invalid, log and bail.
        if (!$this->getSettings()->validate()) {
            Craft::warning('Invalid plugin configuration.', __METHOD__);

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
}

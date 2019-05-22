<?php
namespace timkelty\craftcms\registrar;

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

        if (!$this->getSettings()->validate()) {
            // TODO: log error
            exit(var_dump($this->getSettings()->getErrors()));
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

<?php
namespace timkelty\craftcms\registrar;

use timkelty\craftcms\registrar\models\Settings;

class Plugin extends \craft\base\Plugin
{
    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }
}

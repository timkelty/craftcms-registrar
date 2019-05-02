<?php
/**
 * Registrar plugin for Craft CMS 3.x
 *
 * Allow public registration with configurable access restrictions.
 *
 * @link      https://github.com/timkelty
 * @copyright Copyright (c) 2019 Tim Kelty
 */

namespace timkeltycraftcms\registrar\assetbundles\Registrar;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Tim Kelty
 * @package   Registrar
 * @since     0.1.0
 */
class RegistrarAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@timkeltycraftcms/registrar/assetbundles/registrar/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/Registrar.js',
        ];

        $this->css = [
            'css/Registrar.css',
        ];

        parent::init();
    }
}

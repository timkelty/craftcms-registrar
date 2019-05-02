<?php
/**
 * Registrar plugin for Craft CMS 3.x
 *
 * Allow public registration with configurable access restrictions.
 *
 * @link      https://github.com/timkelty
 * @copyright Copyright (c) 2019 Tim Kelty
 */

namespace timkeltycraftcms\registrar\assetbundles\indexcpsection;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Tim Kelty
 * @package   Registrar
 * @since     0.1.0
 */
class IndexCPSectionAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@timkeltycraftcms/registrar/assetbundles/indexcpsection/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/Index.js',
        ];

        $this->css = [
            'css/Index.css',
        ];

        parent::init();
    }
}

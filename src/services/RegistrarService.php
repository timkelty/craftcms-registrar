<?php
/**
 * Registrar plugin for Craft CMS 3.x
 *
 * Allow public registration with configurable access restrictions.
 *
 * @link      https://github.com/timkelty
 * @copyright Copyright (c) 2019 Tim Kelty
 */

namespace timkeltycraftcms\registrar\services;

use timkeltycraftcms\registrar\Registrar;

use Craft;
use craft\base\Component;

/**
 * @author    Tim Kelty
 * @package   Registrar
 * @since     0.1.0
 */
class RegistrarService extends Component
{
    // Public Methods
    // =========================================================================

    /*
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';
        // Check our Plugin's settings for `someAttribute`
        if (Registrar::$plugin->getSettings()->someAttribute) {
        }

        return $result;
    }
}

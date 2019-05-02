<?php
/**
 * Registrar plugin for Craft CMS 3.x
 *
 * Allow public registration with configurable access restrictions.
 *
 * @link      https://github.com/timkelty
 * @copyright Copyright (c) 2019 Tim Kelty
 */

namespace timkeltycraftcms\registrar\models;

use timkeltycraftcms\registrar\Registrar;

use Craft;
use craft\base\Model;

/**
 * @author    Tim Kelty
 * @package   Registrar
 * @since     0.1.0
 */
class RegistrarModel extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $someAttribute = 'Some Default';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['someAttribute', 'string'],
            ['someAttribute', 'default', 'value' => 'Some Default'],
        ];
    }
}

<?php
namespace timkelty\craftcms\registrar\models;

use Craft;
use timkelty\craftcms\registrar\Registrar;

class Settings extends \craft\base\Model
{
    /**
     * @var string
     */
    public $someAttribute = 'Some Default';

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

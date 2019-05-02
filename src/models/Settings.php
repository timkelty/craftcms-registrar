<?php
namespace timkelty\craftcms\registrar\models;

use Craft;

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

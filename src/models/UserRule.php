<?php
namespace timkelty\craftcms\registrar\models;

use Craft;

class UserRule extends \craft\base\Model
{
    public $attribute;
    public $pattern;
    public $user;

    /**
     * @inheritdoc
     */

    public function init()
    {
        parent::init();
    }

    public function rules()
    {
        return [
            ['attribute', 'string', 'default', 'value' => 'email'],
            ['pattern', 'string'],
        ];
    }
}

<?php
namespace timkelty\craftcms\registrar\models;

use Craft;

class UserRule extends \craft\base\Model
{
    public $attribute;
    public $validator;
    public $options;
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
            [['attribute', 'validator'], 'required'],
            ['attribute', 'string'],
            ['attribute', 'default', 'value' => 'email'],
            ['validator', 'default', 'value' => 'match'],
        ];
    }
}

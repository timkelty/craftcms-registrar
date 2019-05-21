<?php
namespace timkelty\craftcms\registrar\models;

use Craft;

class UserRule extends \craft\base\Model
{
    public $attribute = 'email';
    public $validator = 'match';
    public $options;
    public $user;

    /**
     * @inheritdoc
     */

    // public function init()
    // {
    //     parent::init();
    // }

    public function rules()
    {
        return [
            [['attribute', 'validator'], 'required'],
            ['attribute', 'string'],
        ];
    }
}

<?php
namespace timkelty\craftcms\registrar\models;

use Craft;

class Settings extends \craft\base\Model
{
    /**
     * @var string
     */
    public $rules = [];

    /**
     * @inheritdoc
     */

    public function init()
    {
        parent::init();

        $this->rules[] = [
            'attribute' => 'email',
            'pattern' => '/@fusionary\.com$/',
            'user' => [
                'admin' => true,
            ]

            // 'user' => function ($user) {
            //     $user->admin = true;
            // }
        ];
    }
    // public function rules()
    // {
    //     return [
    //         ['someAttribute', 'string'],
    //         ['someAttribute', 'default', 'value' => 'Some Default'],
    //     ];
    // }
}

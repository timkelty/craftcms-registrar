<?php
namespace timkelty\craftcms\registrar\models;

use Craft;

class Settings extends \craft\base\Model
{
    /**
     * @var array
     */
    public $rules = [];

    /**
     * @var bool
     */
    public $requireRule = true;

    /**
     * @inheritdoc
     */

    public function init()
    {
        parent::init();

        $this->rules[] = [
            'options' => [
                'pattern' => '/@fusionary\.com$/',
                'message' => '{attribute} must be a fusionary.com address.'
            ],
            'user' => [
                'admin' => true
            ]

            // 'user' => function ($user) {
            //     $user->admin = true;
            // }
        ];

        // $this->rules[] = [
        //     'options' => [
        //         'pattern' => '/@gmail\.com$/',
        //         'message' => 'Email must be from a gmail.com domain.'
        //     ],
        //     'user' => [
        //         'admin' => true
        //     ]

        //     // 'user' => function ($user) {
        //     //     $user->admin = true;
        //     // }
        // ];

    }
    // public function rules()
    // {
    //     return [
    //         ['someAttribute', 'string'],
    //         ['someAttribute', 'default', 'value' => 'Some Default'],
    //     ];
    // }
}

<?php
namespace timkelty\craftcms\registrar\models;

use Craft;
use timkelty\craftcms\registrar\validators\ArrayValidator;

class Settings extends \craft\base\Model
{
    /**
     * @var array
     */
    private $tests = [];

    /**
     * @var bool
     */
    public $requireValidation = false;

    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'tests'
        ]);
    }

    public function setTests($tests)
    {
        $this->tests = $tests;
    }

    public function getTests()
    {
        // If not array, let validation take care of it
        if (!is_array($this->tests)) {
            return $this->tests;
        }

        return array_map(function ($test) {
            return $test instanceof RegistrationTest ? $test : new RegistrationTest($test);
        }, $this->tests);
    }

    public function rules()
    {
        return [
            [['requireValidation'], 'required'],
            ['tests', ArrayValidator::class, 'callback' => function ($value) {
                return $value instanceof RegistrationTest;
            }],
        ];
    }
}

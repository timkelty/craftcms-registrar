<?php
namespace timkelty\craftcms\registrar\models;

use craft\validators\ArrayValidator;
use timkelty\craftcms\registrar\Plugin;

class Settings extends \craft\base\Model
{
    /**
     * @var bool
     */
    public $requireValidatedTest = false;

    /**
     * @var bool
     */
    public $debug = false;

    /**
     * @var array
     */
    private $_tests;

    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'tests'
        ]);
    }

    public function setTests($tests)
    {
        $this->_tests = $tests;
    }

    public function getTests()
    {
        if (is_array($this->_tests)) {
            $this->_tests = array_map(function ($test) {
                return $test instanceof RegistrationTest ? $test : new RegistrationTest($test);
            }, $this->_tests);
        }

        return $this->_tests;
    }

    public function validateTests($attribute)
    {
        foreach ($this->$attribute as $key => $test) {
            if (!$test->validate()) {
                $this->addErrors($test->getErrors());
            }
        }

        return null;
    }

    public function afterValidate()
    {
        foreach ($this->getErrors() as $attribute => $errors) {
            foreach ($errors as $error) {
                Plugin::error(
                    Plugin::t('Invalid settings: {error}', [
                        'error' => $error
                    ]),
                    __METHOD__
                );
            }
        }
    }

    public function rules()
    {
        return [
            [['requireValidatedTest', 'debug'], 'boolean'],
            ['tests', ArrayValidator::class],
            ['tests', 'validateTests'],
        ];
    }
}

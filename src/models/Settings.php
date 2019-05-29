<?php
namespace timkelty\craftcms\registrar\models;

use timkelty\craftcms\registrar\validators\CollectionValidator;

class Settings extends \craft\base\Model
{
    /**
     * @var bool
     */
    public $requireValidation = false;

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

    public function rules()
    {
        return [
            [['requireValidation'], 'boolean'],
            ['tests', CollectionValidator::class, 'instanceOf' => RegistrationTest::class]
        ];
    }
}

<?php
namespace timkelty\craftcms\registrar\models;

use craft\validators\ArrayValidator;
use timkelty\craftcms\registrar\Plugin;

class Settings extends \craft\base\Model
{
    use LogErrorsTrait;

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

    public function rules()
    {
        return [
            [['requireValidatedTest', 'debug'], 'boolean'],
            ['tests', ArrayValidator::class],
        ];
    }
}

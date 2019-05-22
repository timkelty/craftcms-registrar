<?php
namespace timkelty\craftcms\registrar\models;

use Craft;

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
        $attributes = parent::attributes();
        $attributes[] = 'tests';

        return $attributes;
    }

    public function setTests($tests)
    {
        $this->tests = $tests ?? [];
    }

    public function getTests()
    {
        return array_map(function ($test) {
            return $test instanceof RegistrationTest ? $test : new RegistrationTest($test);
        }, $this->tests);
    }

    public function rules()
    {
        // TODO: Validate tests with craft\validators\ArrayValidator?
        return [
            [['requireValidation', 'tests'], 'required'],
        ];
    }
}

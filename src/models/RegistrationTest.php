<?php
namespace timkelty\craftcms\registrar\models;

use Craft;
use timkelty\craftcms\registrar\Plugin;
use craft\models\UserGroup;

class RegistrationTest extends \craft\base\Model
{
    public $attribute = 'email';
    public $validator = 'match';
    public $options;
    public $user;
    public $permissions;

    private $_groups;
    private $_groupIds;

    public function setGroups($groups)
    {
        $this->_groups = $groups;
    }

    public function getGroupIds()
    {
        $groups = $this->getGroups();

        if (is_array($groups)) {
            $this->_groupIds = array_map(function ($group) {
                return $group->id;
            }, $groups);
        }

        return $this->_groupIds;
    }

    public function getGroups()
    {
        if (is_array($this->_groups)) {
            $this->_groups = array_unique(array_filter(array_map(function ($group) {
                $group = $group instanceof UserGroup ? $group : Craft::$app->getUserGroups()->getGroupByHandle($group);

                if (!$group) {
                    Craft::warning(
                        Plugin::t(
                            'Invalid user group handle: "{handle}".',
                            ['handle' => $group]
                        ),
                        __METHOD__
                    );
                }

                return $group;
            }, $this->_groups)));
        }

        return $this->_groups;
    }

    public function validateArrayOrCallable($attribute)
    {
        if (is_callable($this->$attribute) || is_array($this->$attribute)) {
            return null;
        }

        $validator = new \yii\validators\Validator;
        $validator->addError($this, $attribute, '{attribute} must be an array or callable.');
    }

    public function rules()
    {
        return [
            [['attribute', 'validator'], 'required'],
            ['attribute', 'string'],
            ['groups', \craft\validators\ArrayValidator::class],
            ['permissions', \craft\validators\ArrayValidator::class],
            ['options', \craft\validators\ArrayValidator::class],
            ['user', 'validateArrayOrCallable'],
        ];
    }
}

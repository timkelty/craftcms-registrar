<?php
namespace timkelty\craftcms\registrar\models;

use Craft;
use craft\models\UserGroup;
use craft\validators\ArrayValidator;
use timkelty\craftcms\registrar\Plugin;
use yii\validators\Validator;

class RegistrationTest extends \craft\base\Model
{
    public $attribute = 'email';
    public $validator = 'match';
    public $value;
    public $options;
    public $user;
    public $permissions;

    private $_groups;
    private $_groupIds;

    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'groups'
        ]);
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

    public function setGroups($groups)
    {
        $this->_groups = $groups;
    }

    public function getGroups()
    {
        if (is_array($this->_groups)) {
            $this->_groups = array_unique(array_filter(array_map(function ($handle) {
                $group = $handle instanceof UserGroup ? $handle : Craft::$app->getUserGroups()->getGroupByHandle($handle);

                if (!$group) {
                    $this->addError('groups', Plugin::t(
                        'Invalid user group handle: "{handle}".',
                        ['handle' => $handle]
                    ));
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

        // TODO: don't need to do this, jsut addError to this model
        $validator = new Validator;
        $validator->addError($this, $attribute, '{attribute} must be an array or callable.');
    }

    public function rules()
    {
        return [
            [['attribute', 'validator'], 'required'],
            ['attribute', 'string'],
            ['options', ArrayValidator::class],
            ['user', 'validateArrayOrCallable'],
            ['groups', ArrayValidator::class],
            ['permissions', ArrayValidator::class],
        ];
    }
}

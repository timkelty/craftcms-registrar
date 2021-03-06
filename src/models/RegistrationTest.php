<?php
namespace timkelty\craftcms\registrar\models;

use Craft;
use craft\models\UserGroup;
use craft\validators\ArrayValidator;
use timkelty\craftcms\registrar\Plugin;

class RegistrationTest extends \craft\base\Model
{
    use LogErrorsTrait;

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
                    Plugin::error(Plugin::t(
                        'Invalid user group handle: "{handle}".',
                        ['handle' => $handle]
                    ), __METHOD__);
                }

                return $group;
            }, $this->_groups)));
        }

        return $this->_groups;
    }

    public function rules()
    {
        return [
            [['attribute', 'validator'], 'required'],
            [['attribute', 'validator'], 'string'],
            [['options', 'user', 'groups', 'permissions'], ArrayValidator::class],
        ];
    }
}

<?php
namespace timkelty\craftcms\registrar\models;

use Craft;
use timkelty\craftcms\registrar\Plugin;

class RegistrationTest extends \craft\base\Model
{
    public $attribute = 'email';
    public $validator = 'match';
    public $options;
    public $user;
    public $permissions;

    private $_groups = [];
    private $_groupIds = [];

    public function setGroupIds($groupIds)
    {
        $this->_groupIds = is_array($groupIds) ? $groupIds : [];
    }

    public function setGroups($groups)
    {
        $this->_groups = is_array($groups) ? $groups : [];
    }

    public function getGroupIds()
    {
        return array_map(function ($group) {
            return $group->id;
        }, $this->getGroups());
    }

    public function getGroups()
    {
        $groups = array_map(function ($group) {
            $group = $group instanceof craft\models\UserGroup ? $group : Craft::$app->getUserGroups()->getGroupByHandle($group);

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
        }, $this->_groups);

        $groupsById = array_map(function ($groupId) {
            $group = Craft::$app->getUserGroups()->getGroupById($groupId);

            if (!$group) {
                Craft::warning(
                    Plugin::t(
                        'Invalid user group id: "{id}".',
                        ['id' => $groupId]
                    ),
                    __METHOD__
                );
            }

            return $group;
        }, $this->_groupIds);

        $this->_groups = array_unique(array_filter(array_merge($groups, $groupsById)));

        return $this->_groups;
    }

    public function rules()
    {
        return [
            [['attribute', 'validator'], 'required'],
            ['attribute', 'string'],
        ];
    }
}

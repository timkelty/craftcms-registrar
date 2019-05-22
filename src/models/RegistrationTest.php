<?php
namespace timkelty\craftcms\registrar\models;

use Craft;

class RegistrationTest extends \craft\base\Model
{
    public $attribute = 'email';
    public $validator = 'match';
    public $options;
    public $user;
    public $permissions;

    private $_groupIds = [];

    public function getGroupIds()
    {
        return $this->_groupIds;
    }

    public function setGroupIds($groupIds)
    {
        $this->_groupIds = array_merge($this->_groupIds, $groupIds);
    }

    public function setGroups($groups)
    {
        $groupIds = array_map(function ($handle) {
            return Craft::$app->getUserGroups()->getGroupByHandle($handle)->id;
        }, $groups);

        $this->setGroupIds($groupIds);
    }

    public function rules()
    {
        return [
            [['attribute', 'validator'], 'required'],
            ['attribute', 'string'],
        ];
    }
}

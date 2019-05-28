<?php
namespace timkelty\craftcms\registrar\models;

use Craft;
use timkelty\craftcms\registrar\Plugin;
use timkelty\craftcms\registrar\validators\CollectionValidator;
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
        $this->_groupIds = array_map(function ($group) {
            return $group->id;
        }, $this->getGroups());

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

    public function rules()
    {
        return [
            [['attribute', 'validator'], 'required'],
            ['attribute', 'string'],
            ['groups', CollectionValidator::class, 'instanceOf' => UserGroup::class],
            ['permissions', \craft\validators\ArrayValidator::class],
            ['options', \craft\validators\ArrayValidator::class],
        ];
    }
}

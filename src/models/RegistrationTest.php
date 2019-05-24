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

    private $groupIds = [];

    public function getGroupIds()
    {
        return $this->groupIds;
    }

    public function setGroupIds($groupIds)
    {
        $this->groupIds = array_merge($this->groupIds, $groupIds);
    }

    public function setGroups($groups)
    {
        $groupIds = array_map(function ($handle) {
            $group = Craft::$app->getUserGroups()->getGroupByHandle($handle);

            if (!$group) {
                Craft::warning(
                    Plugin::t(
                        'Invalid user group handle: "{handle}".',
                        ['handle' => $handle]
                    ),
                    __METHOD__
                );

                return null;
            }

            return $group->id;
        }, $groups);

        $this->setGroupIds(array_filter($groupIds));
    }

    public function rules()
    {
        return [
            [['attribute', 'validator'], 'required'],
            ['attribute', 'string'],
        ];
    }
}

<?php
namespace timkelty\craftcms\registrar;

use timkelty\craftcms\registrar\models\Settings;
use timkelty\craftcms\registrar\models\UserRule;
use timkelty\craftcms\registrar\behaviors\UserBehavior;
use yii\base\Event;
use yii\base\DynamicModel;
use craft\elements\User;
use craft\events\ModelEvent;
use craft\events\DefineRulesEvent;
use Craft;

class Plugin extends \craft\base\Plugin
{

    public function init()
    {
        parent::init();

        Event::on(
            User::class,
            User::EVENT_BEFORE_SAVE,
            function (ModelEvent $event) {
                if (!$event->isNew) {
                    return;
                }

                $user = $event->sender;
                $settings = $this->getSettings();

                $valid = array_filter($settings->rules, function ($rule) use ($user) {
                    $rule = $rule instanceof UserRule ? $rule : new UserRule($rule);
                    $attribute = $rule->attribute;
                    $validator = new \yii\validators\RegularExpressionValidator([
                        'pattern' => $rule->pattern,
                    ]);

                    if ($validator->validate($user->$attribute)) {
                        Craft::configure($user, $rule->user);

                        return true;
                    }

                    $user->addError($attribute, Craft::t('app', 'Invalid Pattern.'));
                });

                return true;
            }
        );
    }

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }
}

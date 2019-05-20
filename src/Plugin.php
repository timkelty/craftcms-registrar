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

                    $rule->validate();

                    $model = new DynamicModel([
                        $rule->attribute => $user->{$rule->attribute}
                    ]);

                    $model->addRule($rule->attribute, $rule->validator, $rule->options);

                    if ($model->validate()) {

                        // TODO: should this be an event?
                        if (is_callable($rule->user)) {
                            call_user_func($rule->user, $user);
                        } else {
                            Craft::configure($user, $rule->user);
                        }

                        return true;
                    } else {
                        $user->addErrors($model->getErrors());

                        return false;
                    }
                });

                if (empty($valid) && $settings->requireRule) {
                    $event->isValid = false;
                }
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

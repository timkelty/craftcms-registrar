<?php

namespace timkelty\craftcms\registrar\validators;

use Craft;
use timkelty\craftcms\registrar\Plugin;
use craft\helpers\StringHelper;
use yii\base\InvalidConfigException;

/**
 * Class ArrayValidator.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.0
 */
class CollectionValidator extends \craft\validators\ArrayValidator
{
  public $instanceOf = \yii\base\Model::class;
  public $validateInstances = true;
  public $notInstanceOf;
  public $notValidInstances;

  public function init()
  {
    parent::init();

    if (!$this->instanceOf) {
      throw new InvalidConfigException('The "instanceOf" property must be set.');
    }

    $this->notInstanceOf = $this->notInstanceOf ?? Plugin::t('{attribute} must contain only instances of {instanceOf}.');
    $this->notValidInstances = $this->notValidInstances ?? Plugin::t('{attribute} must contain valid instances: {errors}.');
  }

  /**
   * @inheritdoc
   * TODO: remove on next Craft release
   */
  public function validateAttribute($model, $attribute)
  {
    $result = $this->validateValue($model->$attribute);

    if (!empty($result)) {
      $this->addError($model, $attribute, $result[0], $result[1]);
    }
  }

  /**
   * @inheritdoc
   */
  protected function validateValue($value)
  {
    parent::validateValue($value);

    // TODO: remove on next Craft release
    if (!$value instanceof \Countable && !is_array($value)) {
      return [$this->message, []];
    }

    $params = ['instanceOf' => $this->instanceOf];

    foreach ($value as $instance) {
      if (!$instance instanceof $this->instanceOf) {
        return [$this->notInstanceOf, $params];
      }

      if ($this->validateInstances && !$instance->validate()) {
        $params['errors'] = StringHelper::toString($instance->getErrors());

        return [$this->notValidInstances, $params];
      }
    }

    return null;
  }
}

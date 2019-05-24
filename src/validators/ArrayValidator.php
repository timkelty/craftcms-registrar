<?php

namespace timkelty\craftcms\registrar\validators;

use Craft;

/**
 * Class ArrayValidator.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.0
 */
class ArrayValidator extends \craft\validators\ArrayValidator
{
  public $callback;
  public $invalidElements;

  /**
   * @inheritdoc
   */
  public function init()
  {
    parent::init();

    if (is_callable($this->callback) && $this->invalidElements === null) {
      $this->invalidElements = Craft::t('app', '{attribute} must be an array with valid elements.');
    }
  }

  /**
   * @inheritdoc
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
    if (!$value instanceof \Countable && !is_array($value)) {
      return [$this->message, []];
    }

    if (is_callable($this->callback)) {
      foreach ($value as $k => $v) {
        if (!call_user_func($this->callback, $v, $k)) {
          return [$this->invalidElements, []];
        }
      }
    }

    $count = count((array)$value);

    if ($this->min !== null && $count < $this->min) {
      return [$this->tooFew, ['min' => $this->min]];
    }
    if ($this->max !== null && $count > $this->max) {
      return [$this->tooMany, ['max' => $this->max]];
    }
    if ($this->count !== null && $count !== $this->count) {
      return [$this->notEqual, ['count' => $this->count]];
    }

    return null;
  }
}

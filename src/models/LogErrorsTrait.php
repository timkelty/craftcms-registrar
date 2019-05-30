<?php
namespace timkelty\craftcms\registrar\models;

use timkelty\craftcms\registrar\Plugin;

trait LogErrorsTrait
{
  public function afterValidate()
  {
    $this->logErrors();
  }

  public function logErrors()
  {
    foreach ($this->getErrors() as $attribute => $errors) {
      foreach ($errors as $error) {
        Plugin::error(
          Plugin::t('{class}: {error}', [
            'class' => __CLASS__,
            'error' => $error,
          ]),
          __METHOD__
        );
      }
    }
  }
}

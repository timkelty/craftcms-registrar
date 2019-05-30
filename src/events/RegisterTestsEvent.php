<?php
namespace timkelty\craftcms\registrar\events;

use craft\elements\User;
use yii\base\Event;

class RegisterTestsEvent extends Event
{
  /**
   * @var User|null The user attempting to register
   */
  public $user;

  /**
   * @var array
   */
  public $tests = [];
}

<?php
namespace timkelty\craftcms\registrar\events;

use craft\elements\User;
use yii\base\Event;

class RegisterTestsEvent extends Event
{
  // Properties
  // =========================================================================

  /**
   * @var User|null The user associated with the event
   */
  public $user;

  /**
   * @var array
   */
  public $tests = [];
}

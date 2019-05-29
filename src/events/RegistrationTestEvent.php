<?php
namespace timkelty\craftcms\registrar\events;

use craft\events\ModelEvent;

class RegistrationTestEvent extends ModelEvent
{
  public $user;
}

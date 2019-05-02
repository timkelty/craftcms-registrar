<?php
namespace timkelty\craftcms\registrar\controllers;

use Craft;

class DefaultController extends \craft\web\Controller
{
    protected $allowAnonymous = ['do-something'];

    /**
     * @return mixed
     */
    public function actionDoSomething()
    {
        $result = 'Welcome to the DefaultController actionDoSomething() method';

        return $result;
    }
}

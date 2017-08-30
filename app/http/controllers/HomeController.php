<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 18:00
 */

namespace app\http\controllers;

use app\components\BaseController;

/**
 * Class HomeController
 * @package app\http\controllers
 */
class HomeController extends BaseController
{
    /**
     * test action
     */
    public function indexAction()
    {
        return __METHOD__;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 18:00
 */

namespace App\http\controllers;

use App\components\BaseController;

/**
 * Class HomeController
 * @package App\http\controllers
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
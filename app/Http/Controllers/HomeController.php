<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 18:00
 */

namespace App\Http\Controllers;

use App\components\BaseController;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class HomeController extends BaseController
{
    /**
     * test action
     */
    public function indexAction($ctx)
    {
        return var_export($ctx, 1);
    }
}
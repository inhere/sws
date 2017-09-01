<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 18:00
 */

namespace App\Http\Controllers;

use App\components\BaseController;
use Sws\Context\HttpContext;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class HomeController extends BaseController
{
    /**
     * test action
     * @param HttpContext $ctx
     * @return string
     */
    public function indexAction($ctx)
    {
        $text = var_export($ctx, 1);

        return "<pre>$text</pre>";
    }
}
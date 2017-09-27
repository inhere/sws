<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 18:00
 */

namespace App\Http\Controllers;

use App\components\BaseController;
use Sws\Annotations\Tags\Controller;
use Sws\Annotations\Tags\Parameter;
use Sws\Annotations\Tags\Parameters;
use Sws\Annotations\Tags\Route;
use Sws\Context\HttpContext;

/**
 * Class HomeController
 * @package App\Http\Controllers
 *
 * @Controller(prefix="/home")
 */
class HomeController extends BaseController
{
    /**
     * test action
     *
     * @Route("index", method="GET")
     * @Parameters({
     *     @Parameter("name", type="string", rule="string; length:2,10;", required),
     *     @Parameter("age", type="int", rule="number; length:2,10;", required = true),
     *     @Parameter("sex", type="int", rule="in:0,1;", default="0")
     * })
     *
     * @param HttpContext $ctx
     * @return string
     */
    public function indexAction($ctx)
    {
        $text = var_export($ctx, 1);

        return "<pre>$text</pre>";
    }
}
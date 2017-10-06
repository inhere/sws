<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 18:00
 */

namespace App\Http\Controllers;

use Sws\Annotations\Tags\Controller;
use Sws\Annotations\Tags\Parameter;
use Sws\Annotations\Tags\Parameters;
use Sws\Annotations\Tags\Route;
use Sws\Context\HttpContext;
use Sws\Web\BaseController;

/**
 * Class HomeController
 * @package App\Http\Controllers
 *
 * @Controller(prefix="/test")
 */
class TestController extends BaseController
{
    /**
     * test action
     *
     * @Route("index", method="GET")
     * @Parameters({
     *     @Parameter("name", type="string", rule="string; length:2,10;", required=true),
     *     @Parameter("age", type="int", rule="number; length:2,10;", required=true),
     *     @Parameter("sex", type="int", rule="in:0,1;", default="0")
     * })
     *
     * @param HttpContext $ctx
     * @return string
     */
    public function indexAction($ctx)
    {
        $text = dump_vars($ctx, $this->getContext());

        return "<pre>$text</pre>";
    }

    /**
     * @Route("/demo")
     * @return string
     */
    public function demoAction()
    {
        $id = spl_object_hash($this);

        return "<pre>$id</pre>";
    }

    /**
     * @Route("/demo1")
     * @return string
     */
    public function demo1Action()
    {
        $id = spl_object_hash($this);

        return "<pre>$id</pre>";
    }
}

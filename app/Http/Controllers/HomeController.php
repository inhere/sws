<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 18:00
 */

namespace App\Http\Controllers;

use App\Logic\DemoLogic;
use Inhere\Library\Helpers\PhpHelper;
use Inhere\Route\ORouter;
use Monolog\Logger;
use Sws\Annotations\Tags\Controller;
use Sws\Annotations\Tags\DI;
use Sws\Annotations\Tags\Parameter;
use Sws\Annotations\Tags\Parameters;
use Sws\Annotations\Tags\Route;
use Sws\Web\HttpContext;
use Sws\Web\BaseController;

/**
 * Class HomeController
 * @package App\Http\Controllers
 *
 * @Controller(prefix="/home")
 */
class HomeController extends BaseController
{
    /**
     * @DI()
     * @var DemoLogic
     */
    private $demoLogic;

    /**
     * test action
     *
     * @Route({"", "index", "/"}, method={"GET", "POST"})
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
        $text = dump_vars(spl_object_hash($ctx), spl_object_hash($this->getContext()));

        return "<pre>$text</pre>";
    }

    /**
     * @Route("/hi/{name}", method="GET", tokens={"name"="\w+"})
     * @return string
     */
    public function hiAction()
    {
        $name = $this->getRequest()->getAttribute('name');

        return "<h2>hi, {$name}</h2>";
    }

    /**
     * @Route(method="GET")
     * @return string
     */
    public function logAction()
    {
        \Sws::$di->get('server')->log('info log');
        \Sws::$di->get('server')->log('error log', [], Logger::ERROR);
        $e = new \RuntimeException('exception log');
        $err = PhpHelper::exceptionToString($e, true, true);
        \Sws::$di->get('server')->log($err, [], Logger::ERROR);
        \Sws::$di->get('server')->log('notice log', [], Logger::NOTICE);

        return 'log test';
    }

    /**
     * @Route("/routes", method="GET")
     * @param $ctx
     * @return string
     */
    public function routesAction(HttpContext $ctx)
    {
        /** @var ORouter $router */
        $router = \Sws::get('httpRouter');

        $ctx->getResponse()->setHeader('Content-Type', 'application/json;charset=utf-8');

        return json_encode([
            'static' => $router->getStaticRoutes(),
            'regular' => $router->getRegularRoutes(),
        ]);
    }

    /**
     * @Route(method="POST")
     * @return string
     */
    public function testAction()
    {
        $id = spl_object_hash($this);

        return "<pre>$id</pre>";
    }

    /**
     * @Route(path="/test1")
     * @return string
     */
    public function test1Action()
    {
        $id = spl_object_hash($this);

        return "<pre>$id</pre>";
    }

    /**
     * @Route()
     * @return string
     */
    public function test2Action()
    {
        $id = spl_object_hash($this);

        return "<pre>$id</pre>";
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 18:00
 */

namespace App\Http\Controllers;

use App\Helper\ResCode;
use Sws\Annotations\Tags\Controller;
use Sws\Annotations\Tags\Parameter;
use Sws\Annotations\Tags\Parameters;
use Sws\Annotations\Tags\Route;
use Sws\Helper\Respond;
use Sws\Web\HttpContext;
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
     * @Route("/my[/{name}[/{age}]]", tokens={"age"="[1-9][0-9]?"})
     * @param HttpContext $ctx
     * @param array $args
     * @return string
     */
    public function myAction(HttpContext $ctx, array $args)
    {
        return Respond::fmtJson([
            'args' => $args,
            'im' => "my name is {$args['name']}" . (isset($args['age']) ? " and age {$args['age']}" : '')
        ], ResCode::OK, 'test optional url argument [age]');
    }

    /**
     * @Route("{id}/detail", tokens={"id"="\d+"})
     * @param HttpContext $ctx
     * @param array $args
     * @return string
     */
    public function detailAction(HttpContext $ctx, array $args)
    {
        return Respond::fmtJson([
            'args from request' => $ctx->getRequest()->getAttributes(),
            'args from method' => $args,
        ]);
    }

    /**
     * @Route("/{name}/profile")
     * @return string
     */
    public function profileAction()
    {
        return Respond::fmtJson($this->getRequest()->getAttributes());
    }

    /**
     * @Route({"zhPy", "zh-py"})
     */
    public function zh2pyAction()
    {
        $raw = '带着希望去旅行';
        $str = \Sws::$di['pinyin']->permalink($raw); // dai-zhe-xi-wang-qu-lv-xing

        return Respond::json([
            'raw' => $raw,
            'pinyin' => $str,
        ]);
    }

    /**
     * @Route("error")
     */
    public function errorAction()
    {
        trigger_error('trigger a user error', E_USER_ERROR);
    }

    /**
     * @Route("exp")
     */
    public function expAction()
    {
        throw new \RuntimeException('throw a exception');
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-30
 * Time: 17:31
 */

namespace Sws\Web;

use inhere\sroute\Dispatcher;
use Sws\Context\HttpContext;

/**
 * Class RouteDispatcher
 * - 为swoole的http请求做了一些处理，将上下文对象 `$context` 作为 action 的唯一参数传入要执行的action 方法
 * @package Sws\Web
 */
class RouteDispatcher extends Dispatcher
{
    /**
     * 当前请求的上下文对象
     * 包含：
     * - request 请求对象
     * - response 响应对象
     * - rid 本次请求的唯一ID(根据此ID 可以获取到原始的 swoole request)
     * - args 路由的参数信息
     * @var HttpContext
     */
    private $context;

    /**
     * @inheritdoc
     */
    protected function executeRouteHandler($path, $method, $handler, array $args = [])
    {
        if ($context = $this->context) {
            $context->setArgs($args);
            $args = [$context];
        }

        $result = parent::executeRouteHandler($path, $method, $handler, $args);

        // restore
        $this->context = null;

        return $result;
    }

    /**
     * @param $context
     * @return $this
     */
    public function send( $context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return HttpContext
     */
    public function getContext(): HttpContext
    {
        return $this->context;
    }

    /**
     * @param HttpContext $context
     * @return $this
     */
    public function setContext(HttpContext $context)
    {
        $this->context = $context;

        return $this;
    }
}
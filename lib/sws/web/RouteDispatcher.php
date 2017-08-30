<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-30
 * Time: 17:31
 */

namespace sws\web;

use inhere\sroute\Dispatcher;
use sws\http\Context;

/**
 * Class RouteDispatcher
 * - 为swoole的http请求做了一些处理，将上下文对象 `$context` 作为 action 的唯一参数传入要执行的action 方法
 * @package sws\web
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
     * @var Context
     */
    private $context;

    /**
     * @param string $path
     * @param callable $handler
     * @param array $args
     * @return mixed
     */
    protected function executeRouteHandler($path, $handler, array $args = [])
    {
        if ($context = $this->context) {
            $context->setArgs($args);
            $args = [$context];
        }

        $result = parent::executeRouteHandler($path, $handler, $args);

        // restore
        $this->context = null;

        return $result;
    }

    /**
     * @param $context
     * @return $this
     */
    public function send(Context $context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return Context
     */
    public function getContext(): Context
    {
        return $this->context;
    }

    /**
     * @param Context $context
     */
    public function setContext(Context $context)
    {
        $this->context = $context;
    }
}
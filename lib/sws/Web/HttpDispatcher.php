<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-30
 * Time: 17:16
 */

namespace Sws\Web;


use Inhere\Pool\ObjectPool;
use Inhere\Route\Dispatcher;
use Inhere\Route\ORouter;

/**
 * Class HttpDispatcher
 * @package Sws\Web
 */
class HttpDispatcher extends Dispatcher
{
    /**
     * execute the matched Route Handler
     * @param string $path The route path
     * @param string $method The request method
     * @param callable $handler The route path handler
     * @param array $args Matched param from path
     * @param array $prependArgs
     * @return mixed
     */
    protected function executeRouteHandler($path, $method, $handler, array $args = [], array $prependArgs = [])
    {
        if ($args) {
            \Sws::getRequest()->setAttribute('args', $args);
        }

        // is a \Closure or a callable object
        if (is_object($handler)) {
            // push prepend args
            $args = array_merge($prependArgs, $args);

            return $handler(...$args);
        }

        //// $handler is string

        // is array ['controller', 'action']
        if (is_array($handler)) {
            $segments = $handler;
        } elseif (is_string($handler)) {
            if (strpos($handler, '@') === false && function_exists($handler)) {
                // push prepend args
                $args = array_merge($prependArgs, $args);
                return $handler(...$args);
            }

            // e.g `controllers\Home@index` Or only `controllers\Home`
            $segments = explode('@', trim($handler));
        } else {
            throw new \InvalidArgumentException('Invalid route handler');
        }

        // Instantiation controller
//        $controller = new $segments[0]();
        $controller = ObjectPool::get($segments[0]);

        if (isset($segments[1])) {
            $action = $segments[1];

            // use dynamic action
        } elseif ((bool)$this->getConfig('dynamicAction')) {
            $action = isset($args[0]) ? trim($args[0], '/') : $this->getConfig('defaultAction');

            // defined default action
        } elseif (!$action = $this->getConfig('defaultAction')) {
            throw new \RuntimeException("please config the route path [$path] controller action to call");
        }

        $args = array_merge($prependArgs, $args);
        $action = ORouter::convertNodeStr($action);
        $actionMethod = $action . $this->getConfig('actionSuffix');

        if ($executor = $this->getConfig('actionExecutor')) {
            $result = $controller->$executor($actionMethod, $args);
            ObjectPool::put($controller);

            return $result;
        }

        if (!$action || !method_exists($controller, $actionMethod)) {
            return $this->handleNotFound($path, $method, true);
        }

        $result = $controller->$actionMethod(...$args);

        ObjectPool::put($controller);

        return $result;
    }
}

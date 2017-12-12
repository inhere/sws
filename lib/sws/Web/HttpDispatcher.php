<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-30
 * Time: 17:16
 */

namespace Sws\Web;

use Inhere\Library\Helpers\Obj;
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
     * {@inheritdoc}
     */
    protected function callRouteHandler($path, $method, $handler, array $args = [])
    {
        if ($vars = $args['matches']) {
            \Sws::$app->getRequest()->setAttributes($vars);
        }

        $args = array_values($args);

        // is a \Closure or a callable object
        if (\is_object($handler)) {
            return $handler(...$args);
        }

        //// $handler is string

        // is array ['controller', 'action']
        if (\is_array($handler)) {
            $segments = $handler;
        } elseif (\is_string($handler)) {
            if (strpos($handler, '@') === false && \function_exists($handler)) {
                return $handler(...$args);
            }

            // e.g `controllers\Home@index` Or only `controllers\Home`
            $segments = explode('@', trim($handler));
        } else {
            throw new \InvalidArgumentException('Invalid route handler');
        }

        // Instantiation controller
        // $controller = new $segments[0]();
        $controller = Obj::get($segments[0]);

        if (isset($segments[1])) {
            $action = $segments[1];

            // use dynamic action
        } elseif ($this->config['dynamicAction'] && ($var = $this->config['dynamicActionVar'])) {
            $action = isset($vars[$var]) ? trim($vars[$var], '/') : $this->config['defaultAction'];

            // defined default action
        } elseif (!$action = $this->getConfig('defaultAction')) {
            throw new \RuntimeException("please config the route path [$path] controller action to call");
        }

        $action = ORouter::convertNodeStr($action);
        $actionMethod = $action . $this->getConfig('actionSuffix');

        if ($executor = $this->getConfig('actionExecutor')) {
            $result = $controller->$executor($actionMethod, $args);
            Obj::put($controller);

            return $result;
        }

        if (!$action || !method_exists($controller, $actionMethod)) {
            return $this->handleNotFound($path, $method, true);
        }

        $result = $controller->$actionMethod(...$args);

        Obj::put($controller);

        return $result;
    }
}

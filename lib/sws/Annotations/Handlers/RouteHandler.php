<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/9/25
 * Time: 下午11:46
 */

namespace Sws\Annotations\Handlers;

use Inhere\Library\Helpers\Obj;
use Inhere\Route\ORouter;
use Sws\Annotations\Collector;
use Sws\Annotations\Position;
use Sws\Annotations\Tags\Controller;
use Sws\Annotations\Tags\Route;
use Sws\Web\BaseController;

/**
 * Class RouteHandler
 * @package Sws\Annotations\Handlers
 */
class RouteHandler extends AbstractHandler
{
    public $controllerSuffix = 'Controller';

    public $actionSuffix = 'Action';

    /**
     * {@inheritdoc}
     */
    public function __invoke(array $classAnn, \ReflectionClass $classRef, Collector $collector)
    {
        /** @var Controller $conf */
        if (!$conf = $classAnn[Controller::class] ?? null) {
            return;
        }

        $class = $classRef->getName();
        /** @var BaseController $object */
        $object = new $class;

        // todo rest ful support...
//        if ($isRest = $conf->type === Controller::REST) {
        $object->setType((int)$conf->type);
//        }

        $actions = [];
        $basename = basename($classRef->getFileName(), '.php');
        // if not setting prefix, will use the controller name. e.g `HomeController -> home`
        $prefix = $conf->prefix ?: lcfirst(str_replace($this->controllerSuffix, '', $basename));

        /** @var ORouter $router */
        $router = \Sws::get('httpRouter');

        foreach ($collector->getAnnotations($class, Position::AT_METHOD) as $mName => $mAnn) {
            /** @var Route $route */
            if (!$route = $mAnn[Route::class] ?? null) {
                continue;
            }

            // e.g. `indexAction -> index`
            $action = str_replace($this->actionSuffix, '', $mName);
            $actions[$action] = $mName;
            $path = $route->path ?? $action;
            $handler = $class . '@' . $action;
            $opts = [
                'tokens' => $route->tokens,
                'schemes' => $route->schemes,
                'domains' => $route->domains,
                'enter' => $route->enter,
                'leave' => $route->leave,
            ];

            // Allows you to register multiple routes to one method
            if (is_array($path)) {
                foreach ($path as $p) {
                    $router->map($route->method, $this->getRealPath($p, $prefix), $handler, $opts);
                }
            } else {
                $router->map($route->method, $this->getRealPath($path, $prefix), $handler, $opts);
            }
        }

        $object->setActions($actions);

        // store the object
        Obj::put($object);
    }

    /**
     * @param string $path
     * @param string $prefix
     * @return string
     */
    private function getRealPath($path, $prefix)
    {
        if (!$path) {
            $path = $prefix;
        } elseif ($path{0} !== '/') {
            $path = $prefix . '/' . $path;
        }

        return $path;
    }
}

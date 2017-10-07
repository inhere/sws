<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/9/25
 * Time: 下午11:46
 */

namespace Sws\Annotations\Handlers;

use Inhere\Pool\ObjectPool;
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

        foreach ($collector->getAnnotationsByType($class, Position::AT_METHOD) as $mName => $mAnn) {
            /** @var Route $route */
            if (!$route = $mAnn[Route::class] ?? null) {
                continue;
            }

            // e.g. `indexAction -> index`
            $action = str_replace($this->actionSuffix, '', $mName);
            $actions[$action] = $mName;
            $path = $route->path ?: $action;

            if ($path{0} !== '/') {
                $path = $prefix . '/' . $path;
            }

            $router->map($route->method, $path, $class . '@' . $action);
        }

        $object->setActions($actions);

        // store the controller object
        ObjectPool::put($object);

//        var_dump($conf, $basename, $class, $prefix);die;
    }
}

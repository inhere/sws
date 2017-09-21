<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 17:36
 */

use inhere\library\di\Container;
use inhere\library\helpers\Arr;
use inhere\libraryPlus\web\ViewRenderer;
use Inhere\Server\Rpc\RpcDispatcher;
use Sws\Memory\Language;
use Sws\Web\RouteDispatcher;

return Arr::merge(require __DIR__ . '/_base.php', [
    'services' => [
        // basic
        'language' => [
            'target' => Language::class,
            'lang' => 'zh-CN',
            'langs' => ['en', 'zh-CN'],
            'basePath' => dirname(__DIR__) . '/resources/langs',
        ],

        // http
        'httpRouter' => [
            'ignoreLastSep' => true,
            'tmpCacheNumber' => 200,
        ],
        'httpDispatcher' => function (Container $di) {
            $dispatcher = new RouteDispatcher([
                'filterFavicon' => true,
                'dynamicAction' => true,
                RouteDispatcher::ON_NOT_FOUND => '/404'
            ]);

            $router = $di->get('httpRouter');

            $dispatcher->setMatcher(function ($path, $method) use($router) {
                return $router->match($path, $method);
            });

            return $dispatcher;
        },
        'renderer' => [
            'target' => ViewRenderer::class,
            'viewsPath' => dirname(__DIR__) . '/resources/views',
        ],

        // rpc
        'rpcDispatcher' => function () {
            $dispatcher = new RpcDispatcher([
                'filterFavicon' => true,
                'dynamicAction' => true,
                RpcDispatcher::ON_NOT_FOUND => 'noService'
            ]);

            // register services
            require BASE_PATH . '/app/rpc/services.php';

            return $dispatcher;
        },
    ],
]);

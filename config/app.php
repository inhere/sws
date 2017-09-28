<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 17:36
 */

use inhere\library\helpers\Arr;
use Inhere\Route\ORouter;
use Inhere\Route\Dispatcher;
use Inhere\Server\Rpc\RpcClient;
use Inhere\Server\Rpc\RpcDispatcher;
use Sws\Memory\Language;
use Sws\Web\ViewRenderer;

return Arr::merge(require __DIR__ . '/_base.php', [
    'configCenter' => [
        'addr' => '127.0.0.1:5456',
    ],

    'assets' => [
        'ext' => [],
        'dirMap' => [
            // 'url_match' => 'assets dir',
            '/assets' => 'web/assets',
            '/uploads' => 'web/uploads'
        ]
    ],
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
            'target' => ORouter::class,
            'ignoreLastSep' => true,
            'tmpCacheNumber' => 200,
        ],
        'httpDispatcher' => [
            'target' => Dispatcher::class,
            'config' => [
                'filterFavicon' => true,
                'dynamicAction' => true,
                Dispatcher::ON_NOT_FOUND => '/404'
            ],
            'matcher' => function ($path, $method) {
                /** @var ORouter $router */
                $router = \Sws::$app->get('router');
                return $router->match($path, $method);
            },
        ],
        'renderer' => [
            'target' => ViewRenderer::class,
            'viewsPath' => dirname(__DIR__) . '/resources/views',
        ],

        // rpc services consumer(client)
        'rpcClient' => [
            'target' => RpcClient::class,
        ],

        // rpc services provider(server)'s Dispatcher
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

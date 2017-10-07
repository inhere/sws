<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 17:36
 */

use Inhere\Library\Helpers\Arr;
use Inhere\Route\ORouter;
use Inhere\Server\Rpc\RpcClient;
use Inhere\Server\Rpc\RpcDispatcher;
use Sws\Memory\Language;
use Sws\Web\HttpDispatcher;
use Sws\Web\ViewRenderer;

return Arr::merge(require __DIR__ . '/_base.php', [
    'configCenter' => [
        'addr' => '127.0.0.1:5456',
    ],

    'logger' => [
        'name' => 'app',
        'file' => '@tmp/logs/app/application.log',
        'level' => \Monolog\Logger::DEBUG,
        'splitType' => 1,
        'bufferSize' => 0, // 0 1000,
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
            '_options' => ['active' => 1],
            'lang' => 'zh-CN',
            'langs' => ['en', 'zh-CN'],
            'basePath' => dirname(__DIR__) . '/resources/langs',
        ],

        // http
        'httpRouter' => [
            'target' => ORouter::class,
            '_options' => ['active' => 1],
            'config' => [
                'ignoreLastSep' => true,
                'tmpCacheNumber' => 200,
            ]
        ],
        'httpDispatcher' => [
            'target' => HttpDispatcher::class,
            '_options' => ['active' => 1],
            'config' => [
                'filterFavicon' => true,
                'dynamicAction' => true,
                HttpDispatcher::ON_NOT_FOUND => '/404'
            ],
            'matcher' => function ($path, $method) {
                /** @var ORouter $router */
                $router = \Sws::$app->get('httpRouter');
                return $router->match($path, $method);
            },

        ],
        'renderer' => [
            'target' => ViewRenderer::class,
            '_options' => ['active' => 1],
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

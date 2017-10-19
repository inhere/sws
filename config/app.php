<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 17:36
 */

use Inhere\Library\Helpers\Arr;
use Inhere\Library\Web\ViewRenderer;
use Inhere\Route\ORouter;
use Inhere\Server\Rpc\RpcClient;
use Inhere\Server\Rpc\RpcDispatcher;
use Overtrue\Pinyin\MemoryFileDictLoader;
use Overtrue\Pinyin\Pinyin;
use Sws\Memory\Language;
use Sws\Web\ContextManager;
use Sws\Web\HttpDispatcher;
use Sws\WebSocket\ConnectionManager;

return Arr::merge(require __DIR__ . '/_base.php', [
    'configCenter' => [
        'addr' => '127.0.0.1:5456',
    ],

    'application' => [
        'openGzip' => true,
        'gzipLevel' => 1,
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

    /**
     * register service to container
     * @see \Inhere\Library\DI\Container::set()
     */
    'services' => [
        /**
         * basic service
         */

        'language' => [
            'target' => Language::class,
            'lang' => 'zh-CN',
            'langs' => ['en', 'zh-CN'],
            'basePath' => dirname(__DIR__) . '/resources/langs',
            '_options' => ['active' => 1, 'aliases' => ['lang']],
        ],
        'pinyin' => [
            'target' => Pinyin::class,
            '_args' => [MemoryFileDictLoader::class],
            '_options' => ['active' => 1, 'aliases' => ['zhTransfer']],
        ],

        /**
         * http service
         */

        'ctxManager' => [
            'target' => ContextManager::class,
            '_options' => ['active' => 1, 'aliases' => ['contextManager']],
        ],
        'httpRouter' => [
            'target' => ORouter::class,
            'config' => [
                'ignoreLastSep' => true,
                'tmpCacheNumber' => 200,
            ],
            '_options' => ['active' => 1],
        ],
        'httpDispatcher' => [
            'target' => HttpDispatcher::class,
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
            '_options' => ['active' => 1],

        ],
        'renderer' => [
            'target' => ViewRenderer::class,
            'viewsPath' => dirname(__DIR__) . '/resources/views',
            '_options' => ['active' => 1, 'aliases' => 'viewRenderer'],
        ],

        /**
         * websocket service
         */

        'cnnManager' => [
            'target' => ConnectionManager::class,
            '_options' => ['active' => 1, 'aliases' => ['connectionManager']],
        ],

        /**
         * rpc service
         */

        // rpc services consumer(client)
        'rpcClient' => [
            'target' => RpcClient::class,
        ],

        // rpc services provider(server)'s Dispatcher
        'rpcDispatcher' => function () {
            $dispatcher = new RpcDispatcher();

            // register services
            require BASE_PATH . '/app/rpc/services.php';

            return $dispatcher;
        },
    ],
]);

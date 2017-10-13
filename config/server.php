<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 17:28
 */

return [
    'debug' => true,
    'name' => 'svr',
    'root_path' => BASE_PATH,
    'pid_file' => BASE_PATH . '/tmp/sws.pid',
    'auto_reload' => 'app,config',

    'error_handler' => \Sws\Components\ErrorHandler::class,
    'log' => [
        'name' => 'server',
        'file' => BASE_PATH . '/tmp/logs/server/server.log',
        'level' => \Monolog\Logger::DEBUG,
        'splitType' => 1,
        'bufferSize' => 0, //0 1000,
    ],

    // for current main server/ outside extend server.
    // @see \Inhere\Server\Traits\HttpServerTrait::$options
    'options' => [
        'ignoreFavicon' => true,
    ],

    // main server
    'main_server' => [
        'type' => 'ws', // http https tcp udp ws wss
        'port' => 9501,
        'extend_events' => [
            'onConnect',
            'onRequest', // 增加 http 请求支持
        ],
    ],

    // attach port server by config
    'attach_servers' => [
        'port1' => [
            'host' => '0.0.0.0',
            'port' => '9761',
            'type' => 'udp',
            // must setting the handler class in config.
            'listener' => \Inhere\Server\PortListeners\UdpListener::class,
        ],
        'rpcServer' => [
            'listener' => \Sws\Rpc\Application::class,
            'host' => '0.0.0.0',
            'port' => '9762',
        ]
    ],

    'swoole' => [
        'user'    => 'www-data',
        'worker_num'    => 2,
        'task_worker_num' => 1,
        'daemonize'     => false,
        'max_request'   => 10000,
        'log_file' => BASE_PATH . '/tmp/logs/swoole.log',

        'upload_tmp_dir' => BASE_PATH . '/tmp/upFiles/',
//        'document_root' => BASE_PATH . '/web',
//        'enable_static_handler' => true,
    ]
];

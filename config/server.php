<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 17:28
 */

use Sws\server\WebSocketServer;

return [
    'debug' => true,
    'name' => 'demo',
    'root_path' => BASE_PATH,
    'pid_file' => BASE_PATH . '/tmp/test_server.pid',
    'log_service' => [
        'name'     => 'slim_server',
        'basePath' => BASE_PATH . '/tmp/logs/test_server',
        'logThreshold' => 0,
    ],
    'auto_reload' => 'app,config',

    // for current main server/ outside extend server.
    'options' => [

    ],

    // main server
    'main_server' => [
        'type' => 'ws', // http https tcp udp ws wss
        'port' => 9501,
        //'extend_server' => WebSocketServer::class,
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
            'listener' => \inhere\server\portListeners\UdpListener::class,
        ]
    ],

    'swoole' => [
        'user'    => 'www-data',
        'worker_num'    => 4,
        'task_worker_num' => 2,
        'daemonize'     => false,
        'max_request'   => 10000,
        // 'log_file' => PROJECT_PATH . '/temp/logs/slim_server_swoole.log',
    ]
];
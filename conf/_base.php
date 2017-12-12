<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 16:19
 */

use Monolog\Logger;

return [
    'debug' => false,
    'env'   => 'pdt',
    'charset' => 'UTF-8',
    'timeZone' => 'Asia/Shanghai',
    'rootPath' => dirname(__DIR__),

    'enableCsrfToken' => true,

    'role' => [
        'provider', // 服务提供方
        'consumer' // 服务消费方
    ],

    'application' => [
        'name' => 'app',
    ],
    'logger' => [
        'name' => 'app',
        'file' => '@tmp/logs/app/application.log',
        'level' => Logger::DEBUG,
        'splitType' => 1,
        'bufferSize' => 1000, // 1000,
    ],

    // 扫描注解包(命名空间)路径
    'annotation' => [

    ],

    'services' => [
        // log service
//        'fileLogHandler' => [
//            'target' => FileLogHandler::class,
//        ],

//        'dbLogHandler' => [
//            'target' => FileLogHandler::class,
//        ],

//        'logger' => [
//            'target' => Logger::class,
//            'handlers' => [
//                'file' => '@{fileLogHandler}',
////                'db' => '@{dbLogHandler}',
//            ]
//        ],

        // db service
//        'db' => [
//            'debug' => '&{debug}',
//        ]

    ]

];

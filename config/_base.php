<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 16:19
 */

use inhere\library\log\Logger;
use Sws\Memory\FileLogHandler;

return [
    'debug' => false,
    'env'   => 'pdt',
    'rootPath' => dirname(__DIR__),

    'assets' => [
        'ext' => [],
        'dirMap' => [
            // 'url_match' => 'assets dir',
            '/assets' => 'web/assets',
            '/uploads' => 'web/uploads'
        ]
    ],
    'role' => [
        'provider', // 服务提供方
        'consumer' // 服务消费方
    ],

    // 扫描注解包(命名空间)路径，多个包用逗号分隔，不填表示扫描当前ApplicationContext中所有的类
    'annotation' => [

    ],

    'services' => [
        // log service
        'fileLogHandler' => [
            'target' => FileLogHandler::class,
        ],

//        'dbLogHandler' => [
//            'target' => FileLogHandler::class,
//        ],

        'logger' => [
            'target' => Logger::class,
            'handlers' => [
                'file' => '@{fileLogHandler}',
//                'db' => '@{dbLogHandler}',
            ]
        ],

        // db service
//        'db' => [
//            'debug' => '&{debug}',
//        ]

    ]

];

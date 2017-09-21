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

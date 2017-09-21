<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 17:35
 */

use inhere\library\components\Language;
use inhere\library\helpers\Arr;

return Arr::merge(require __DIR__ . '/_base.php',[
    'logger' => [

    ],

    'services' => [
        'language' => [
            'target' => Language::class,
            'lang' => 'zh-CN',
            'langs' => ['en', 'zh-CN'],
            'files' => [
                'default.php',
                'user.php',
            ],
        ],

    ],
]);

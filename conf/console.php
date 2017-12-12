<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 17:35
 */

use Inhere\Library\Components\Language;
use Inhere\Library\Helpers\Arr;

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

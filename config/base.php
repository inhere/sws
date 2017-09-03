<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 16:19
 */

return [
    'assets' => [
        'ext' => [],
        'map' => [
            // 'url_match' => 'assets dir',
            '/assets' => 'web/assets',
            '/uploads' => 'web/uploads'
        ]
    ],

    'language' => [
        'language' => 'zh-CN',
        'languages' => ['en', 'zh-CN'],
        'files' => [
          'default.php',
          'user.php',
        ],
    ],
];

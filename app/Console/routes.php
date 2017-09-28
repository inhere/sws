<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-03-17
 * Time: 11:20
 * @var \Inhere\Console\Application $app
 */

$app->commands([
    'test' => function () {
        echo 'test';
    },
//    'demo' => \App\console\commands\DemoCommand::class,
    //'book:build' => \App\console\commands\BuildCommand::class,
]);

$app->controllers([
    \App\Console\Controllers\DemoController::class,
]);

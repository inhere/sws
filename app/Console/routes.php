<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-03-17
 * Time: 11:20
 * @var \Sws\Console\Application $app
 */

use App\Console\Commands\BuildCommand;

$app->commands([
    'test' => function () {
        echo 'test';
    },
//    'demo' => \App\console\commands\DemoCommand::class,
    //'book:build' => \App\console\commands\BuildCommand::class,
    BuildCommand::class,
]);

$app->controllers([
    \App\Console\Controllers\DemoController::class,
]);

<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-03-17
 * Time: 11:20
 * @var \inhere\console\App $app
 */


$app->commands([
    'test' => function () {
        echo 'test';
    },
//    'demo' => \app\console\commands\DemoCommand::class,
    //'book:build' => \app\console\commands\BuildCommand::class,
]);

$app->controllers([
//    'home' => \app\console\controllers\HomeController::class,
]);

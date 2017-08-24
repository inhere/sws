<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-03-17
 * Time: 11:20
 * @var \inhere\console\App $app
 */

$app->commands([
    // src/console/commands/DemoCommand.php
    'demo' => \app\console\commands\DemoCommand::class,
    'book:export' => \app\console\commands\ExportCommand::class,
    'book:import' => \app\console\commands\ImportCommand::class,
    //'book:build' => \app\console\commands\BuildCommand::class,
]);

$app->controllers([
    'home' => \app\console\controllers\HomeController::class,
    'cache' => \app\console\controllers\CacheController::class,
    'search' => \app\console\controllers\CacheController::class,
]);

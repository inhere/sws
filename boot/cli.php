<?php

use inhere\library\collections\Collection;

require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/di.php';

$di->set('config', function () {
    $basePath = dirname(__DIR__);
    $locFile = $basePath . '/.local';
    $local = is_file($locFile) ? Collection::parseIni($locFile) : [
        'env' => 'pdt',
        'rootPath' => $basePath
    ];

    // load config
    return Collection::make($basePath . '/config/cli.php','php','console')
        ->loadArray($basePath . "/config/cli/{$local['env']}.php")
        ->loadArray($local);
});

$di->set('app', function ($di) {
    $app = new \app\Cli\App();
    $app->setDi($di);

    // register commands
    require dirname(__DIR__) . '/app/cli/routes.php';
    return $app;
});

error_reporting(E_ALL);

define('RUNTIME_ENV', $di['config']->get('env'));
define('APP_DEBUG', $di['config']->get('debug'));

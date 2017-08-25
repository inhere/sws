<?php

use inhere\library\collections\Collection;

require BASE_PATH . '/vendor/autoload.php';
require __DIR__ . '/di.php';


$di->set('config', function () {
    $locFile = BASE_PATH . '/.local';
    $local = is_file($locFile) ? Collection::parseIni($locFile) : [
        'env' => 'pdt',
        'rootPath' => BASE_PATH
    ];

    // load config
    return Collection::make(
        BASE_PATH . '/config/console.php',
        'php',
        'console'
    )
        ->loadArray(BASE_PATH . "/config/cli/{$local['env']}.php")
        ->loadArray($local);
});

$di->set('app', function ($di) {
    $app = new \app\cli\App();
    $app->setDi($di);

    // register commands
    require BASE_PATH . '/app/cli/routes.php';

    return $app;
});

error_reporting(E_ALL);

define('RUNTIME_ENV', $di['config']->get('env'));
define('APP_DEBUG', $di['config']->get('debug'));

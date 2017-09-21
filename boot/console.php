<?php

use inhere\library\collections\Configuration;

// autoload
require dirname(__DIR__) . '/vendor/autoload.php';

// create service container
require __DIR__ . '/container.php';

$di->set('config', function () {
    return Configuration::makeByEnv(
        dirname(__DIR__) . '/.local', // locFile
        dirname(__DIR__)  . '/config/console.php', // baseFile
        dirname(__DIR__)  . '/config/console/{env}.php' // envFile
    );
});

$di->set('app', function ($di) {
    $app = new \App\Console\Application();
    $app->setDi($di);

    // register commands
    require dirname(__DIR__) . '/app/Console/routes.php';
    return $app;
});

//
/** @var Configuration $config */
$config = $di->get('config');

// load config services
$di->sets($config->remove('services'));

error_reporting(E_ALL);
define('RUNTIME_ENV', $config->get('env'));
define('APP_DEBUG', $config->get('debug'));

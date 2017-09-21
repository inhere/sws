<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-25
 * Time: 9:39
 */

use inhere\library\collections\Configuration;

// autoload
require dirname(__DIR__) . '/vendor/autoload.php';

// create service container
require __DIR__ . '/container.php';

$di->set('config', function () {
    return Configuration::makeByEnv(
        dirname(__DIR__) . '/.local', // locFile
        dirname(__DIR__)  . '/config/app.php', // baseFile
        dirname(__DIR__)  . '/config/app/{env}.php' // envFile
    );
});

$di->set('app', function ($di) {
    $config = require BASE_PATH . '/config/server.php';

    \Sws::$app = $app = new \Sws\Application($config);
    $app->setDi($di);

    return $app;
});

/** @var Configuration $config */
$config = $di->get('config');

// load config services
$di->sets($config->remove('services'));

error_reporting(E_ALL);
define('RUNTIME_ENV', $config->get('env'));
define('APP_DEBUG', $config->get('debug'));


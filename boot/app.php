<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-25
 * Time: 9:39
 *
 * @var $di Container
 */

use inhere\library\di\Container;
use inhere\library\collections\Configuration;
use Sws\Async\StreamHandler;

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

$di->set('app', function (Container $di) {
    $config = require BASE_PATH . '/config/server.php';

    \Sws::$app = $app = new \Sws\Application($config);
    $app->setDi($di);

    return $app;
});

$di->set('logger', function (Container $di) {
    $opts = $di->get('config')->get('logger', []);

    $fileHandler = new StreamHandler($opts['file']);
    $mainHandler = new \Monolog\Handler\FingersCrossedHandler($fileHandler, (int)$opts['level'], $opts['bufferSize']);

    $logger = new \Monolog\Logger($opts['name']);
    $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
    $logger->pushHandler($mainHandler);

    return $logger;
});

/** @var Configuration $config */
$config = $di->get('config');

// load config services
$di->sets($config->remove('services'));

error_reporting(E_ALL);
define('RUNTIME_ENV', $config->get('env'));
define('APP_DEBUG', $config->get('debug'));


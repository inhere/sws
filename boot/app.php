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
use Sws\Components\AppLogHandler;
use Sws\Components\ExtraLogger;
use Sws\Server;

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

$di->set('logger', function (Container $di) {
    $opts = $di->get('config')->get('logger', []);

    $file = \Sws::alias($opts['file']);
    $fileHandler = new AppLogHandler($file, (int)$opts['level'], (int)$opts['splitType']);
    $mainHandler = new \Monolog\Handler\FingersCrossedHandler($fileHandler, (int)$opts['level'], $opts['bufferSize']);

    $logger = new ExtraLogger($opts['name']);
    $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
    $logger->pushHandler($mainHandler);

    return $logger;
});

$di->set('server', function (Container $di) {
    $config = require dirname(__DIR__) . '/config/server.php';
    // $sever = new SwsServer($config);

    return new Server($config);
});

$di->set('app', function (Container $di) {
    $opts = $di->get('config')->get('application', []);

    \Sws::$app = $app = new \Sws\Application($opts);
    $app->setServer($di->get('server'));
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


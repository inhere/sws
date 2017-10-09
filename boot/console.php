<?php
/**
 * @var $di Container
 */

use Inhere\Library\DI\Container;
use Inhere\Library\Collections\Configuration;

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

$di->set('logger', function (Container $di) {
    $settings = $di->get('config')->get('logger', []);

    $logger = new \Monolog\Logger($settings['name']);
    $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
    $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['file'], (int)$settings['level']));

    return $logger;
});

// monolog - database logger
$di['dbLogger'] = function (Container $c) {
    $settings = $c->get('config')->get('dbLogger', []);
    $logger = new \Monolog\Logger($settings['name']);
    // $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
    $handler = new \Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG);
    // formatter, ordering log rows
    $handler->setFormatter(new \Monolog\Formatter\LineFormatter("[%datetime%] SQL: %message% \n"));
    $logger->pushHandler($handler);
    return $logger;
};

$di->set('app', function (Container $di) {
    $settings = $di->get('config')->get('application', []);
    $app = new \Sws\Console\Application($settings);
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

//vd($di, -4);

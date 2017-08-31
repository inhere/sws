<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-25
 * Time: 9:39
 */

use inhere\library\collections\Collection;
use inhere\library\di\Container;

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
    return Collection::make($basePath . '/config/web.php','php','web')
        ->loadArray("{$basePath}/config/web/{$local['env']}.php")
        ->loadArray($local);
});

$di->set('app', function ($di) {
    $config = require BASE_PATH . '/config/server.php';

    $app = new \Sws\App($config);
    $app->setDi($di);
    \Sws::$app = $app;

    return $app;
});

$di->set('routeDispatcher', function () {
    return new \inhere\sroute\Dispatcher([
        'filterFavicon' => true,
        'dynamicAction' => true,
    ]);
});

$di->set('router', function (Container $di) {
    $router = new \inhere\sroute\ORouter([
        'ignoreLastSep' => true,
        'tmpCacheNumber' => 200,
    ]);

    $router->setDispatcher($di->get('routeDispatcher'));

    // register routes
    require BASE_PATH . '/app/http/routes.php';

    return $router;
});

// some settings
error_reporting(E_ALL);
define('RUNTIME_ENV', $di['config']->get('env'));
define('APP_DEBUG', $di['config']->get('debug'));
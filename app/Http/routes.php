<?php
/**
 * Routes
 *
 * @var $router \Inhere\Route\ORouter
 */

use App\Http\Controllers\HomeController;
use inhere\library\di\Container;

$router->get('/', function () {
   return 'xxx';
});

$router->get('/404', function () {
   return '404 NOT FOUND!';
});

$router->get('/ws', function () {
   return \Sws::$app->getDi()->get('renderer')->render('ws.html');
});

$router->any('/home', HomeController::class . '@indexAction');
$router->any('/home/{act}', HomeController::class);
<?php
/**
 * Routes
 *
 * @var $router \Inhere\Route\ORouter
 */

use App\Http\Controllers\HomeController;

$router = Sws::$di->get('httpRouter');
$router->get('/', function () {
   return 'xxx';
});

$router->get('/404', function () {
   return '404 NOT FOUND!';
});

$router->get('/ws', function () {
   return \Sws::$di->get('renderer')->render('ws.html');
});

$router->any('/home', HomeController::class . '@index');
$router->any('/home/{act}', HomeController::class);
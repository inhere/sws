<?php
/**
 * Routes
 *
 * @var $router \Inhere\Route\ORouter
 */

use App\Http\Controllers\HomeController;

$router = Sws::$di->get('httpRouter');
//$router->get('/', function () {
//   return 'xxx';
//});

$router->get('/404', function () {
   return '404, PAGE NOT FOUND!';
});

$router->get('/ws', function () {
   return \Sws::$di->get('renderer')->render('ws/index.html');
});

$router->any('/home', HomeController::class . '@index');
$router->any('/home/{act}', HomeController::class);

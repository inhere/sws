<?php
/**
 * Routes
 *
 * @var $router  \inhere\sroute\ORouter
 */

use App\Http\Controllers\HomeController;

$router->get('/', function () {
   return 'xxx';
});

$router->get('/404', function () {
   return '404 NOT FOUND!';
});

$router->any('/home', HomeController::class . '@indexAction');
$router->any('/home/{act}', HomeController::class);
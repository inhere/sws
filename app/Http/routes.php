<?php
/**
 * Routes
 *
 * @var $router  \inhere\sroute\ORouter
 */

use App\http\controllers\HomeController;

$router->get('/', function () {
   return 'xxx';
});

$router->any('/home', HomeController::class . '@indexAction');
$router->any('/home/{act}', HomeController::class);
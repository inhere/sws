<?php
/**
 * Routes
 *
 * @var $router \Inhere\Route\ORouter
 */

use App\Http\Controllers\HomeController;

$router = Sws::get('httpRouter');
$router->get('/', function () {
    return 'xxx';
});

$router->get('/ab', function () {
    return 'hello';
});

$router->get('/404', function () {
    return '404, PAGE NOT FOUND!';
});

$router->get('/ws', function () {
//    \Sws::info(\Sws::get('renderer'));
    return \Sws::get('renderer')->render('ws/index.html');
});

$router->any('/home/{act}', HomeController::class);

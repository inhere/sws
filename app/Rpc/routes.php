<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/9/3
 * Time: 上午1:08
 *
 * @var $router  \inhere\sroute\ORouter
 */

use App\Rpc\TestService;

$router->any('/', function () {
    return 'hello';
});

$router->any('/test', TestService::class . '@demoAction');


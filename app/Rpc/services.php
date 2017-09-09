<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/9/3
 * Time: 上午1:08
 *
 * @var $dispatcher  \inhere\server\rpc\RpcDispatcher
 */

use App\Rpc\TestService;

$dispatcher->add('demo', function () {
    return 'hello';
});

$dispatcher->add('test', TestService::class);


<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-08
 * Time: 15:42
 */

use Swoole\Coroutine;
use Swoole\Coroutine\Redis;
use Swoole\Http\Response;

require dirname(__DIR__) . '/vendor/autoload.php';

function debug($msg, array $data = [])
{
    printf("%s %s %s \n", date('Y-m-d H:i:s'), $msg, $data ? json_encode($data): '');
}

function coro_redis_test() {
    $rds = new Redis();

    debug('coId:' . Coroutine::getuid() . ' will create new redis connection');

    $rds->connect('redis', 6379);

    debug('coId:' . Coroutine::getuid() . ' a new redis connection created');

    $suc = $rds->set('test', 'value');
    $val = $rds->get('test');

    var_dump($suc, $val);
}

$host = '127.0.0.1';
$port = 8399;
$svr = new \Swoole\Http\Server($host, $port);

echo "server run on {$host}:{$port}\n";

$svr->on('request', function ($req, Response $res) {
    coro_redis_test();

    $res->end("hello world!\n");
});

$svr->set([

]);
$svr->start();
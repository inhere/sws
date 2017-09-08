<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-08
 * Time: 15:42
 */

use Swoole\Coroutine;
use Swoole\Coroutine\MySQL;
use Swoole\Http\Response;

require dirname(__DIR__) . '/vendor/autoload.php';

function debug($msg, array $data = [])
{
    printf("%s %s %s \n", date('Y-m-d H:i:s'), $msg, $data ? json_encode($data): '');
}

function coro_mysql_test() {
    $db = new MySQL();

    debug('coId:' . Coroutine::getuid() . ' will create new db connection');

    $db->connect([
        'host' => 'mysql',
        'port' => 3306,
        'user' => 'root',
        'password' => 'password',
        'database' => 'test',
    ]);

    debug('coId:' . Coroutine::getuid() . ' a new db connection created');

    $data = $db->query('show tables');

    var_dump($data);
}

$host = '127.0.0.1';
$port = 8399;
$svr = new \Swoole\Http\Server($host, $port);

echo "server run on {$host}:{$port}\n";

$svr->on('request', function ($req, Response $res) {
    coro_mysql_test();

    $res->end("hello world!\n");
});

$svr->set([

]);
$svr->start();
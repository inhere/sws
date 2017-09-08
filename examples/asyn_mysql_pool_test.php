<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-07
 * Time: 17:42
 */
require dirname(__DIR__) . '/vendor/autoload.php';

use Swoole\Http\Response;
use Sws\Components\AsyncMysqlPool;

$host = '127.0.0.1';
$port = 8399;
$svr = new \Swoole\Http\Server($host, $port);
echo "server run on {$host}:{$port}\n";

function debug($msg, array $data = [])
{
    printf("%s %s %s \n", date('Y-m-d H:i:s'), $msg, $data ? json_encode($data) : '');
}

$pool = new AsyncMysqlPool([
    'initSize' => 0,
    'maxSize' => 1,
]);

var_dump($pool);

$svr->on('request', function ($req, Response $res) use ($pool) {
    $db = $pool->get();

    $db->query('show tables', function ($db, $ret) {
        if ($ret === false) {
            var_dump($db->error, $db->errno);
        } elseif ($ret === true) {
            var_dump($db->affected_rows, $db->insert_id);
        }

        var_dump($ret);

        $db->close();
    });

    $res->end("hello world!\n");
});

$svr->set([

]);

$svr->start();
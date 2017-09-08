<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-07
 * Time: 17:42
 */

use Swoole\Http\Response;

$host = '127.0.0.1';
$port = 8399;
$svr = new \Swoole\Http\Server($host, $port);

echo "server run on {$host}:{$port}\n";

$svr->on('request', function ($req, Response $res) {
    $res->end("hello world!\n");
});

$svr->set([

]);
$svr->start();
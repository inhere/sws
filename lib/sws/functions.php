<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/9/3
 * Time: 上午12:49
 */
namespace Sws;

use Sws\WebSocket\Message;

/**
 * @param string $data
 * @param int $sender
 * @param array $receivers
 * @param array $excepted
 * @return Message
 */
function message(string $data = '', array $receivers = [], array $excepted = [], int $sender = 0)
{
    return Message::make($data, $receivers, $excepted, $sender);
}

function app() {
    return \Sws::$app;
}

function di() {
    return \Sws::$app->getDi();
}

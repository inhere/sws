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

/**
 * @param null|string $service
 * @return mixed
 */
function app($service = null)
{
    return $service ? \Sws::$di->get($service) : \Sws::$app;
}

function get($service = null)
{
    return $service ? \Sws::$di->get($service) : \Sws::$di;
}

function di($service = null)
{
    return $service ? \Sws::$di->get($service) : \Sws::$di;
}

function config($name, $default = null)
{
    return \Sws::$di->get('config')->get($name, $default);
}

function path($path)
{
    return \Sws::alias($path);
}
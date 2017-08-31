<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-28
 * Time: 13:49
 */

namespace Sws\Server;

/**
 * Interface WsServerInterface
 * @package Sws\Server
 */
interface WsServerInterface
{
//    const WS_KEY_PATTEN  = '#^[+/0-9A-Za-z]{21}[AQgw]==$#';

    /**
     * @param $data
     * @param string $msg
     * @param int $code
     * @param \Closure|null $afterMakeMR
     * @param bool $reset
     * @return int
     */
    public function send($data, string $msg = '', int $code = 0, \Closure $afterMakeMR = null, bool $reset = true): int;
}
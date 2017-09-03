<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-28
 * Time: 13:49
 */

namespace Sws\WebSocket;

/**
 * Interface WsServerInterface
 * @package Sws\WebSocket
 */
interface WsServerInterface
{
    // some events
    const EVT_WS_CONNECT = 'wsConnect';
    const EVT_WS_OPEN = 'wsOpen';
    const EVT_WS_DISCONNECT = 'wsDisconnect';
    const EVT_HANDSHAKE_REQUEST = 'handshakeRequest';
    const EVT_HANDSHAKE_SUCCESSFUL = 'handshakeSuccessful';
    const EVT_WS_MESSAGE = 'wsMessage';
    const EVT_WS_CLOSE = 'wsClose';
    const EVT_WS_ERROR = 'wsError';
    const EVT_NO_MODULE = 'noModule';
    const EVT_PARSE_ERROR = 'parseError';

    const WS_VERSION = 13;
    const WS_KEY_PATTEN = '#^[+/0-9A-Za-z]{21}[AQgw]==$#';
    const SIGN_KEY = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';

    const HANDSHAKE_OK = 0;
    const HANDSHAKE_FAIL = 25;


    /**
     * send message to client(s)
     * @param string $data
     * @param int|array $receivers
     * @param int|array $expected
     * @param int $sender
     * @return int
     */
    public function send(string $data, $receivers = 0, $expected = 0, int $sender = 0): int;
}

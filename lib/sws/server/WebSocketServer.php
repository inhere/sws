<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-25
 * Time: 10:53
 */

namespace sws\server;

use inhere\library\traits\EventTrait;
use inhere\server\servers\HttpServer;

use sws\http\Request;
use sws\http\Response;

use Swoole\Http\Response as SwResponse;
use Swoole\Http\Request as SwRequest;
use Swoole\Websocket\Server;
use Swoole\Websocket\Frame;
use sws\http\Uri;

/**
 * Class WebSocketServer
 * @package sws\server
 *
 * @property Server $server
 */
class WebSocketServer extends HttpServer implements WsServerInterface
{
    use EventTrait;

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

    const HANDSHAKE_OK = 0;
    const HANDSHAKE_FAIL = 25;

    /**
     * client total number
     * @var int
     */
    private $clientNumber = 0;

    /**
     * 连接的客户端列表
     * @var resource[]
     * [
     *  id => socket,
     * ]
     */
//    protected $clients = [];

    /**
     * 连接的客户端信息列表
     * @var Connection[]
     * [
     *  cid => Connection
     * ]
     */
    private $connections = [];

////////////////////// WS Server event //////////////////////

    /**
     * 这里还无法判断是否是 webSocket 或者 http
     * @param Server $server
     * @param int $fd
     * @param $fromId
     */
    public function onConnect($server, $fd, $fromId)
    {
        $info = $this->getClientInfo($fd);
        $this->log("onConnect: PID {$server->master_pid}, connection ID: $fd, form reactor ID: $fromId, info: " . var_export($info, 1));

        $cid = $this->resourceId($fd);

        // 触发 connect 事件回调
        $this->fire(self::EVT_WS_CONNECT, [$this, $cid]);
    }

    /**
     * webSocket 建立连接后进行握手。WebSocket服务器已经内置了handshake，
     * 如果用户希望自己进行握手处理，可以设置 onHandShake 事件回调函数。
     *
     * @param SwRequest $swRequest
     * @param SwResponse $swResponse
     * @return bool
     */
    public function onHandShake(SwRequest $swRequest, SwResponse $swResponse)
    {
        $this->log("onHandShake: Client [fd: {$swRequest->fd}] send handShake request");

        $cid = $swRequest->fd;
        $info = $this->getClientInfo($cid);
        // $data = $this->getPeerName($fd);
        $meta = [
            'ip' => $info['remote_ip'],
            'port' => $info['remote_port'],
            'path' => '/',
            'handshake' => false,
            'connectTime' => $info['connect_time'],
            'resourceId' => $cid,
        ];

        // 初始化客户端信息
        $this->connections[$cid] = new Connection($meta);

        // begin start handshake
        $method = $swRequest->server['request_method'];
        $uriStr = $swRequest->server['request_uri'];

        $request = new Request($method, Uri::createFromString($uriStr));
        $request->setHeaders($swRequest->header ?: []);

        if ($cookies = $swRequest->cookie ?? null) {
            $request->setCookies($cookies);
        }

        $this->log("Handshake: Ready to shake hands with the #$cid client connection. request:\n" . $request->toString());

        $response = new Response();
        $secKey = $swRequest->header['sec-websocket-key'];

        // sec-websocket-key 错误
        if ($this->isInvalidSecWSKey($secKey)) {
            $this->log("handshake failed with client #{$cid}! [Sec-WebSocket-Key] not found OR is error in header. request: \n" . $request->toString(), [], 'error');

            $swResponse->status(404);
            $swResponse->write('<b>400 Bad Request</b><br>[Sec-WebSocket-Key] not found in request header.');
            $swResponse->end();

            $this->delConnection($cid);
            return false;
        }

        // 触发 handshake 事件回调，如果返回 false -- 拒绝连接，比如需要认证，限定路由，限定ip，限定domain等
        // 就停止继续处理。并返回信息给客户端
        if (self::HANDSHAKE_FAIL === $this->fire(self::EVT_HANDSHAKE_REQUEST, [$request, $response, $cid])) {
            $this->log("The #$cid client handshake's callback return false, will close the connection");

            $swResponse->status($response->getStatusCode());

            foreach ($response->getHeaders() as $name => $value) {
                $swResponse->header($name, $value);
            }

            if ($body = $response->getBody()) {
                $swResponse->write($body);
            }

            $swResponse->end();
            $this->delConnection($cid);

            return false;
        }

        // setting response
        $response
            ->setStatus(101)
            ->setHeaders([
                'Upgrade' => 'websocket',
                'Connection' => 'Upgrade',
                'Sec-WebSocket-Accept' => $this->genSign($secKey),
                'Sec-WebSocket-Version' => self::WS_VERSION,
            ]);
        $this->debug("Handshake: response info:\n" . $response->toString());

        // 响应握手成功
        $swResponse->status($response->getStatusCode());
        foreach ($response->getHeaders() as $name => $value) {
            $swResponse->header($name, $value);
        }

//        $respData = $response->toString();
//        $this->debug("Handshake: response info:\n" . $respData);
//        $r = $this->server->send($cid, $respData);


        // 客户端连接单独保存
//        $this->clients[$cid] = $socket;
        $this->clientNumber++;
        // 标记已经握手 更新路由 path
        $meta = $this->connections[$cid];
        $meta->setRequest($request);
        $meta['handshake'] = true;
        $meta['path'] = $request->getPath();

        $this->log("Handshake: The #$cid client connection handshake successful! Meta:", $meta->all());
        $this->fire(self::EVT_HANDSHAKE_SUCCESSFUL, [$request, $response, $cid]);

        // 握手成功 触发 open 事件
        $this->server->defer(function () use ($swRequest) {
            $this->onOpen($this->server, $swRequest);
        });

        return true;
    }

    /**
     * webSocket 连接上时
     * @param  Server $server
     * @param  SwRequest $request
     */
    public function onOpen($server, SwRequest $request)
    {
        $cid = $request->fd;
        $this->rid = base_convert(str_replace('.', '', microtime(1)), 10, 16) . "0{$cid}";

        $this->log("onOpen: The Client #{$cid} connection open successful! Meta:", $this->connections[$cid]->all());

        $this->fire(self::EVT_WS_OPEN, [$this, $request, $cid]);

        // var_dump($cid, $request->get, $request->server);
        $server->push($cid, "hello, welcome\n");
    }

    /**
     * webSocket 收到消息时
     * @param  Server $server
     * @param  Frame $frame
     */
    public function onMessage($server, Frame $frame)
    {
        $this->log("onMessage: The Client #{$frame->fd} send message: {$frame->data}");

        // send message to all
        // $this->broadcast($server, $frame->data);

        // send message to fd.
        $server->push($frame->fd, "server: {$frame->data}");
    }

    public function handleWsRequest($server, Frame $frame)
    {
    }

    /**
     * webSocket断开连接
     * @param  Server $server
     * @param  int $fd
     */
    public function onClose($server, $fd)
    {
        /*
        返回数据：
        "websocket_status":0, // 此状态可以判断是否为WebSocket客户端。
        "server_port":9501,
        "server_fd":4,
        "socket_type":1,
        "remote_port":56554,
        "remote_ip":"127.0.0.1",
        "from_id":2,
        "connect_time":1487940465,
        "last_time":1487940465,
        "close_errno":0

        WEBSOCKET_STATUS_CONNECTION = 1，连接进入等待握手
        WEBSOCKET_STATUS_HANDSHAKE = 2，正在握手
        WEBSOCKET_STATUS_FRAME = 3，已握手成功等待浏览器发送数据帧
        */
        $fdInfo = $server->connection_info($fd);

        // is web socket request
        if ($fdInfo['websocket_status'] > 0) {
            $meta = $this->delConnection($fd);

            // call on close callback
//            if ($freeEvent) {
                $this->fire(self::EVT_WS_CLOSE, [$this, $fd, $meta]);
//            }

            $this->log("onClose: The #$fd client connection has been closed! From {$meta['ip']}:{$meta['port']}. Count: {$this->clientNumber}");
            $this->log("onClose: Client #{$fd} is closed", $fdInfo);
        }
    }

    protected function resourceId($resource): int
    {
        return (int)$resource;
    }

    /**
     * @param $cid
     */
    public function close($cid)
    {
        $this->server->close($cid);
    }

////////////////////////////////////////////////////////////////////////
/// message send methods
////////////////////////////////////////////////////////////////////////

    const WS_KEY_PATTEN  = '#^[+/0-9A-Za-z]{21}[AQgw]==$#';
    const SIGN_KEY = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';

    /**
     * Generate WebSocket sign.(for server)
     * @param string $key
     * @return string
     */
    public function genSign(string $key): string
    {
        return base64_encode(sha1(trim($key) . self::SIGN_KEY, true));
    }

    /**
     * @param string $secWSKey 'sec-websocket-key: xxxx'
     * @return bool
     */
    public function isInvalidSecWSKey($secWSKey)
    {
        return 0 === preg_match(self::WS_KEY_PATTEN, $secWSKey) || 16 !== strlen(base64_decode($secWSKey));
    }

    /**
     * check it a accepted client and handshake completed  client
     * @param int $cid
     * @return bool
     */
    public function isHandshake(int $cid): bool
    {
        if ($this->hasConnection($cid)) {
            return $this->getConnection($cid)['handshake'];
        }

        return false;
    }

    /**
     * count handshake clients
     * @return int
     */
    public function countHandshake(): int
    {
        $count = 0;

        foreach ($this->connections as $info) {
            if ($info['handshake']) {
                $count++;
            }
        }

        return $count;
    }

    /**
     *  check it is a exists client
     * @notice maybe don't complete handshake
     * @param $cid
     * @return bool
     */
    public function hasConnection(int $cid)
    {
        return isset($this->connections[$cid]);
    }

    /**
     * @param int $cid
     * @return bool|Connection
     */
    public function getConnection(int $cid)
    {
        if ($this->hasConnection($cid)) {
            return $this->connections[$cid];
        }

        return false;
    }

    /**
     * @param int $cid
     * @return bool|Connection
     */
    public function delConnection(int $cid)
    {
        if ($this->hasConnection($cid)) {
            $meta = $this->connections[$cid];
            $this->clientNumber--;

            unset($this->connections[$cid]);
            return $meta;
        }

        return false;
    }

////////////////////////////////////////////////////////////////////////
/// message send methods
////////////////////////////////////////////////////////////////////////

    /**
     * Send a message to the specified user 发送消息给指定的用户
     * @param int $receiver 接收者
     * @param string $data
     * @param int $sender 发送者
     * @return int
     */
    public function sendTo(int $receiver, string $data, int $sender = 0)
    {
        $finish = true;
        $opcode = 1;
        $fromUser = $sender < 1 ? 'SYSTEM' : $sender;
        $this->log("(private)The #{$fromUser} send message to the user #{$receiver}. Data: {$data}");

        return $this->server->push($receiver, $data, $opcode, $finish) ? 0 : -500;
    }

    /**
     * broadcast message 广播消息
     * @param string $data 消息数据
     * @param int $sender 发送者
     * @param int[] $receivers 指定接收者们
     * @param int[] $expected 要排除的接收者
     * @return int   Return socket last error number code.  gt 0 on failure, eq 0 on success
     */
    public function broadcast(string $data, array $receivers = [], array $expected = [], int $sender = 0): int
    {
        if (!$data) {
            return 0;
        }

        // only one receiver
        if (1 === count($receivers)) {
            return $this->sendTo(array_shift($receivers), $data, $sender);
        }

        // to all
        if (!$expected && !$receivers) {
            $this->sendToAll($data, $sender);

            // to some
        } else {
            $this->sendToSome($data, $receivers, $expected, $sender);
        }

        return $this->getErrorNo();
    }

    /**
     * @param string $data
     * @param int $sender
     * @return int
     */
    public function sendToAll(string $data, int $sender = 0): int
    {
        $startFd = 0;
        $count = 0;
        $fromUser = $sender < 1 ? 'SYSTEM' : $sender;

        $this->log("(broadcast)The #{$fromUser} send a message to all users. Data: {$data}");

        while (true) {
            $connList = $this->server->connection_list($startFd, 50);

            if ($connList === false || ($num = count($connList)) === 0) {
                break;
            }

            $count += $num;
            $startFd = end($connList);

            /** @var $connList array */
            foreach ($connList as $fd) {
                $this->server->push($fd, $data);
            }
        }

        return $count;
    }

    /**
     * @param string $data
     * @param array $receivers
     * @param array $expected
     * @param int $sender
     * @return int
     */
    public function sendToSome(string $data, array $receivers = [], array $expected = [], int $sender = 0): int
    {
        $count = 0;
        $res = $data;
        $len = strlen($res);
        $fromUser = $sender < 1 ? 'SYSTEM' : $sender;

        // to receivers
        if ($receivers) {
            $this->log("(broadcast)The #{$fromUser} gave some specified user sending a message. Data: {$data}");

            foreach ($receivers as $receiver) {
                if ($this->hasConnection($receiver)) {
                    $count++;
                    $this->server->push($receiver, $res, $len);
                }
            }

            return $count;
        }

        // to special users
        $startFd = 0;
        $this->log("(broadcast)The #{$fromUser} send the message to everyone except some people. Data: {$data}");

        while (true) {
            $connList = $this->server->connection_list($startFd, 50);

            if ($connList === false || ($num = count($connList)) === 0) {
                break;
            }

            $count += $num;
            $startFd = end($connList);

            /** @var $connList array */
            foreach ($connList as $fd) {
                if (isset($expected[$fd])) {
                    continue;
                }

                if ($receivers && !isset($receivers[$fd])) {
                    continue;
                }

                $this->server->push($fd, $data);
            }
        }

        return $count;
    }

    /**
     * response data to client by socket connection
     * @param int $fd
     * @param string $data
     * @param int $length
     * @return int   Return error number code. gt 0 on failure, eq 0 on success
     */
    public function writeTo($fd, string $data, int $length = 0)
    {
//        $finish = true;
//        $opcode = 1;
        // return $this->server->push($fd, $data, $opcode, $finish) ? 0 : 1;
        return $this->server->send($fd, $data) ? 0 : 1;
    }

    /**
     * @param int $cid
     * @return bool
     */
    public function exist(int $cid)
    {
        return $this->server->exist($cid);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->clientNumber;
    }

////////////////////////////////////////////////////////////////////////
/// get/set methods
////////////////////////////////////////////////////////////////////////

    /**
     * @return int
     */
    public function getClientNumber(): int
    {
        return $this->clientNumber;
    }

    /**
     * @return Connection[]
     */
    public function getConnections(): array
    {
        return $this->connections;
    }

    /**
     * @param Connection[] $connections
     */
    public function setConnections(array $connections)
    {
        $this->connections = $connections;
    }


}
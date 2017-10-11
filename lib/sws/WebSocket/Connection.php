<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-25
 * Time: 16:18
 */

namespace Sws\WebSocket;

use Inhere\Library\Helpers\Obj;
use Inhere\Http\Request;
use Sws\Context\AbstractContext;

use Swoole\Http\Request as SwRequest;
use Swoole\Http\Response as SwResponse;

/**
 * Class Connection - client connection metadata
 * @package Sws\WebSocket
 */
class Connection extends AbstractContext
{
    /**
     * @var string
     */
    private $ip;

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $path = '/';

    /**
     * @var int
     */
    private $connectTime;

    /**
     * @var bool
     */
    private $handshake = false;

    /**
     * @var int
     */
    private $handshakeTime = 0;

    /**
     * class constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        Obj::configure($this, $config);

        parent::__construct();

        $this->connectTime = time();

        \Sws::getConnectionManager()->add($this);
    }

    /**
     * destroy
     */
    public function destroy()
    {
        \Sws::getConnectionManager()->del($this->getId());

        parent::destroy();
    }

    /**
     * {@inheritdoc}
     */
    public function setRequestResponse(SwRequest $swRequest, SwResponse $swResponse)
    {
        $this->setKey(self::genKey($swRequest->fd));

        parent::setRequestResponse($swRequest, $swResponse);
    }

    /**
     * @return array
     */
    public function all()
    {
        return array_merge(parent::all(),[
            'ip' => $this->ip,
            'port' => $this->port,
            'path' => $this->path,
            'handshake' => $this->handshake,
            'connectTime' => $this->connectTime,
            'handshakeTime' => $this->handshakeTime,
        ]);
    }

    /**
     * handshake
     * @param Request $request
     */
    public function handshake(Request $request)
    {
        $this->path = $request->getPath();
//        $this->request = $request;
        $this->handshake = true;
        $this->handshakeTime = time();
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp(string $ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort(int $port)
    {
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return int
     */
    public function getConnectTime(): int
    {
        return $this->connectTime;
    }

    /**
     * @param int $connectTime
     */
    public function setConnectTime(int $connectTime)
    {
        $this->connectTime = $connectTime;
    }

    /**
     * @return bool
     */
    public function isHandshake(): bool
    {
        return $this->handshake;
    }

    /**
     * @param bool $handshake
     */
    public function setHandshake($handshake)
    {
        $this->handshake = (bool)$handshake;
    }

    /**
     * @return int
     */
    public function getHandshakeTime(): int
    {
        return $this->handshakeTime;
    }
}

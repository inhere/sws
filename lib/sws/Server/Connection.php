<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-25
 * Time: 16:18
 */

namespace Sws\Server;

use inhere\library\StdObject;
use inhere\library\traits\ArrayAccessByPropertyTrait;
use Sws\http\Request;
use Traversable;

/**
 * Class Connection - client connection metadata
 * @package Sws\Server
 */
class Connection extends StdObject implements \ArrayAccess, \IteratorAggregate
{
    use ArrayAccessByPropertyTrait;

    /**
     * @var string
     */
    private $id;

    /**
     * @var int
     */
    private $resourceId;

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
     * @var Request
     */
    private $request;

    /**
     * ClientMetadata constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->connectTime = time();
        $this->generateId();
    }

    /**
     * @return array
     */
    public function all()
    {
        return [
            'id' => $this->id,
            'ip' => $this->ip,
            'port' => $this->port,
            'path' => $this->path,
            'handshake' => $this->handshake,
            'connectTime' => $this->connectTime,
            'handshakeTime' => $this->handshakeTime,
            'resourceId' => $this->resourceId,
        ];
    }

    /**
     * handshake
     * @param Request $request
     */
    public function handshake(Request $request)
    {
        $this->path = $request->getPath();
        $this->request = $request;
        $this->handshake = true;
        $this->handshakeTime = time();
    }

    /**
     * generateClientId
     */
    protected function generateId(): void
    {
        $this->id = bin2hex(random_bytes(32));
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @return int
     */
    public function getResourceId(): int
    {
        return $this->resourceId;
    }

    /**
     * @param int $resourceId
     */
    public function setResourceId(int $resourceId)
    {
        $this->resourceId = $resourceId;
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

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->all());
    }
}
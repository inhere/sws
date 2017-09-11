<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-25
 * Time: 16:18
 */

namespace Sws\WebSocket;

use inhere\library\StdObject;
use inhere\library\traits\ArrayAccessByPropertyTrait;
use Sws\Components\HttpHelper;
use Inhere\Http\Request;
use Inhere\Http\Response;
use Traversable;

use Swoole\Http\Request as SwRequest;
//use Swoole\Http\Response as SwResponse;

/**
 * Class Connection - client connection metadata
 * @package Sws\WebSocket
 */
class Connection extends StdObject implements \ArrayAccess, \IteratorAggregate
{
    use ArrayAccessByPropertyTrait;

    /**
     * it is `request->fd`
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $key;

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
     * @var Response
     */
    private $response;

    /**
     * ClientMetadata constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->connectTime = time();
    }

    /**
     * @return array
     */
    public function all()
    {
        return [
            'id' => $this->id,
            'ip' => $this->ip,
            'key' => $this->key,
            'port' => $this->port,
            'path' => $this->path,
            'handshake' => $this->handshake,
            'connectTime' => $this->connectTime,
            'handshakeTime' => $this->handshakeTime,
        ];
    }

    public function initRequestContext(SwRequest $swRequest)
    {
        $this->key = HttpHelper::genKey($swRequest->fd);

        $this->request = HttpHelper::createRequest($swRequest);
        $this->response = HttpHelper::createResponse();
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
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key)
    {
        $this->key = $key;
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
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
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
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
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

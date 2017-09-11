<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 17:42
 */

namespace Sws\Rpc;

use inhere\library\di\Container;
use Inhere\Server\Rpc\JsonParser;
use Inhere\Server\Rpc\ParserInterface;
use Inhere\Server\Rpc\RpcDispatcher;
use Inhere\Server\Rpc\RpcServerListener;
use Psr\Container\ContainerInterface;
use Swoole\Server;
use Sws\ApplicationInterface;

/**
 * Class Application
 * @package App\Cli
 */
class Application extends RpcServerListener implements ApplicationInterface
{
    /**
     * @var Container
     */
    private $di;

    /**
     * {@inheritDoc}
     */
    public function __construct(array $options = [], ParserInterface $parser = null)
    {
        \Sws::$app = $this;

        $this->parser = $parser ?: new JsonParser();

        parent::__construct($options);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function get($id)
    {
        return $this->di->get($id);
    }

    /**
     * @param ContainerInterface $di
     */
    public function setDi(ContainerInterface $di)
    {
        $this->di = $di;
    }

    /**
     * @return ContainerInterface
     */
    public function getDi()
    {
        return $this->di;
    }

    /**
     * @param Server $server
     * @param string $buffer
     * @param int $fd
     * @throws \Throwable
     */
    protected function handleRpcRequest(Server $server, $buffer, $fd)
    {
        try {
            $request = $this->parser->decode($buffer);

            /** @var RpcDispatcher $dispatcher */
            $dispatcher = $this->di->get('httpDispatcher');
            $resp = $dispatcher->dispatch($request['s'], $request['p']);
            $resp = $this->parser->encode($resp);

            $server->send($fd, $resp);

            $error = $server->getLastError();
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}

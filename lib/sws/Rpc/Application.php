<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 17:42
 */

namespace Sws\Rpc;

use Inhere\Server\Rpc\JsonParser;
use Inhere\Server\Rpc\RpcDispatcher;
use Inhere\Server\Rpc\RpcServerListener;
use Inhere\Server\Rpc\TextParser;
use Swoole\Server;
use Sws\ApplicationInterface;
use Sws\ApplicationTrait;

/**
 * Class Application
 * @package App\Console
 */
class Application extends RpcServerListener implements ApplicationInterface
{
    use ApplicationTrait;

    /**
     * {@inheritDoc}
     */
    public function __construct(array $options = [])
    {
        $options = array_merge([
            'name' => 'rpcSvr',
            'timeoutInMilliseconds' => 1000,
        ], $options);

        parent::__construct($options);

        $this->setParsers([
            new JsonParser(),
            new TextParser(),
        ]);
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
            $dispatcher = \Sws\get('rpcDispatcher');
            $resp = $dispatcher->dispatch($request['s'], $request['p']);
            $resp = $this->parser->encode($resp);

            $server->send($fd, $resp);

            if ($error = $server->getLastError()) {
                \Sws\get('logger')->error($error);
            }

        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getOption('name');
    }
}

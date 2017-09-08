<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-08
 * Time: 17:39
 */

namespace Sws\Rpc;

use inhere\server\rpc\RpcServerListener;
use Swoole\Server;

/**
 * Class RpcPortListener
 * @package Sws\Rpc
 */
class RpcPortListener extends RpcServerListener
{
    protected function handleRpcRequest(Server $server, $data)
    {
        // TODO: Implement handleRpcRequest() method.
    }
}
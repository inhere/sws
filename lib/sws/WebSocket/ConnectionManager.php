<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/9/3
 * Time: 上午1:09
 */

namespace Sws\WebSocket;

use SwooleLib\Context\ContextManager;


/**
 * Class ConnectionManager
 * @package Sws\WebSocket
 */
class ConnectionManager extends ContextManager
{
    /**
     * @return int|string
     */
    protected function getDefaultId()
    {
//        return Coroutine::tid();
    }

    /**
     * @return array
     */
    public function getConnections()
    {
        return $this->getContextList();
    }
}

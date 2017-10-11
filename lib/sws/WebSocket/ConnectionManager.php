<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/9/3
 * Time: 上午1:09
 */

namespace Sws\WebSocket;

use Sws\Web\ContextManager;

/**
 * Class ConnectionManager
 * @package Sws\WebSocket
 */
class ConnectionManager extends ContextManager
{
    /**
     * @return \ArrayIterator
     */
    public function getConnections()
    {
        return $this->getIterator();
    }
}

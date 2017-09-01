<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-31
 * Time: 15:17
 */

namespace Sws\Context;

use Swoole\Http\Request as SwRequest;
use Swoole\Http\Response as SwResponse;

/**
 * Class WsContext
 * @package Sws\Context
 */
class WsContext extends HttpContext
{
    /**
     * @param SwRequest $swRequest
     * @return int
     */
    public static function getUniqueKey(SwRequest $swRequest)
    {
        return $swRequest->fd;
    }

}

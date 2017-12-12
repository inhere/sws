<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/10/7
 * Time: 下午6:15
 */

namespace Sws\Web;

use Swoole\Http\Response as SwResponse;

/**
 * Class Response
 * @package Sws\Web
 */
class Response extends \Inhere\Http\Response
{
    /**
     * @param SwResponse $swResponse
     * @return SwResponse
     */
    public function toSwResponse(SwResponse $swResponse)
    {
        // set http status
        $swResponse->status($this->getStatus());

        // set headers
        foreach ($this->getHeadersObject()->getLines() as $name => $value) {
            $swResponse->header($name, $value);
        }

        // set cookies
        foreach ($this->getCookies()->toHeaders() as $value) {
            $swResponse->header('Set-Cookie', $value);
        }

        // write content
        if ($body = (string)$this->getBody()) {
            $swResponse->write($body);
        }

        return $swResponse;
    }
}

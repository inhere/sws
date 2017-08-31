<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-29
 * Time: 11:38
 */

namespace Sws\Http;

use Swoole\Http\Request as SwRequest;
use Swoole\Http\Response as SwResponse;

/**
 * Class Context
 * @package Sws\Http
 */
class Context
{
    /**
     * @var string
     */
    private $rid;

    /**
     * @var array
     */
    private $args = [];

    public static function make(SwRequest $swRequest, SwResponse $swResponse)
    {

    }

    public function __construct(SwRequest $swRequest, SwResponse $swResponse)
    {

    }

    public function getRequest()
    {

    }

    public function getResponse()
    {

    }

    public function getSwRequest()
    {

    }

    public function getSwResponse()
    {

    }

    public function getLogger()
    {

    }

    /**
     * @return string
     */
    public function getRid(): string
    {
        return $this->rid;
    }

    /**
     * @param string $rid
     */
    public function setRid(string $rid)
    {
        $this->rid = $rid;
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @param array $args
     */
    public function setArgs(array $args)
    {
        $this->args = $args;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-25
 * Time: 9:40
 */

namespace sws;

use inhere\library\di\Container;

/**
 * Class App
 * @package sws
 */
class App
{
    /**
     * @var Container
     */
    private $di;

    public function run()
    {

    }

    public function handleRequest()
    {

    }

    public function handleWsRequest()
    {

    }

    /**
     * @return Container
     */
    public function getDi(): Container
    {
        return $this->di;
    }

    /**
     * @param Container $di
     */
    public function setDi(Container $di)
    {
        $this->di = $di;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 17:42
 */

namespace App\Rpc;

use inhere\library\di\Container;
use Psr\Container\ContainerInterface;
use Sws\ApplicationInterface;

/**
 * Class Application
 * @package App\Cli
 */
class Application implements ApplicationInterface
{
    /**
     * @var Container
     */
    private $di;

    protected function init()
    {
        \Sws::$app = $this;
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
}

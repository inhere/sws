<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-19
 * Time: 15:06
 */

namespace Sws;

use Inhere\Library\DI\Container;

/**
 * Class ApplicationTrait
 * @package Sws
 */
trait ApplicationTrait
{
    /**
     * @var Container
     */
    protected $di;

    /**
     * @param $id
     * @return mixed
     */
    public function get($id)
    {
        return $this->di->get($id);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getIfExist($id)
    {
        return $this->di->getIfExist($id);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function has($id)
    {
        return $this->di->has($id);
    }

    /**
     * @return Container
     */
    public function getDi()
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

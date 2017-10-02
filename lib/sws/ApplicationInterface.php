<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/9/3
 * Time: 上午12:57
 */

namespace Sws;

use Inhere\Library\DI\Container;

/**
 * Class ApplicationInterface
 * @package Sws
 */
interface ApplicationInterface
{
    /**
     * @param $id
     * @return mixed
     */
    public function get($id);

    /**
     * @param $id
     * @return mixed
     */
    public function has($id);

    /**
     * @param Container $di
     */
    public function setDi(Container $di);

    /**
     * @return Container
     */
    public function getDi();
}

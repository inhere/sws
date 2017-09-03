<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/9/3
 * Time: 上午12:57
 */

namespace Sws;

use Psr\Container\ContainerInterface;

/**
 * Class ApplicationInterface
 * @package Sws
 */
interface ApplicationInterface
{
    public function get($id);

    /**
     * @param ContainerInterface $di
     */
    public function setDi(ContainerInterface $di);

    /**
     * @return ContainerInterface
     */
    public function getDi();
}

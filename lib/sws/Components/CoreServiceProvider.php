<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-21
 * Time: 9:43
 */

namespace Sws\Components;

use Inhere\Event\EventManager;
use Inhere\Library\DI\Container;
use Inhere\Library\DI\ServiceProviderInterface;

/**
 * Class CoreServiceProvider
 * @package Sws\Components
 */
class CoreServiceProvider implements ServiceProviderInterface
{
    /**
     * 注册一项服务(可能含有多个服务)提供者到容器中
     * @param Container $di
     */
    public function register(Container $di)
    {
        if (!isset($di['eventManager'])) {
            $di->set('eventManager', function () {
                return new EventManager();
            });
        }
    }
}

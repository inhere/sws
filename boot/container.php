<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 16:19
 *
 * @var $di \Inhere\Library\DI\Container
 */

use Inhere\Library\Components\AopProxy;
use Inhere\Library\DI\ContainerManager;

$di = Sws::$di = ContainerManager::make();

$di->set('aop', function () {
    return new AopProxy();
});

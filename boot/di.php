<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 16:19
 *
 * @var $di \inhere\library\di\Container
 */

use inhere\library\components\AopProxy;
use inhere\library\di\ContainerManager;

$di = ContainerManager::make();

$di->set('aop', function () {
    return new AopProxy();
});
<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/9/25
 * Time: 下午11:44
 */

namespace Sws\Annotations\Handlers;

use Sws\Annotations\Collector;

/**
 * interface HandlerInterface
 * @package Sws\Annotations\Handlers
 */
interface HandlerInterface
{
    /**
     * @param array $classAnn
     * @param \ReflectionClass $classRef
     * @param Collector $collector
     * @return mixed
     */
    public function __invoke(array $classAnn, \ReflectionClass $classRef, Collector $collector);

    public function setCollector(Collector $collector);
}

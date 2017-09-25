<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/9/25
 * Time: 下午11:47
 */

namespace Sws\Annotations\Handlers;

use Sws\Annotations\Collector;

/**
 * Class AbstractHandler
 * @package Sws\Annotations\Handlers
 */
abstract class AbstractHandler implements HandlerInterface
{
    /**
     * @var Collector
     */
    protected $collector;

    /**
     * @param Collector $collector
     */
    public function setCollector(Collector $collector)
    {
        $this->collector = $collector;
    }
}

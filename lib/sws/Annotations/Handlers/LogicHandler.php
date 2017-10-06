<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/9/25
 * Time: 下午11:46
 */

namespace Sws\Annotations\Handlers;

use Sws\Annotations\Collector;
use Sws\Annotations\Tags\Logic;

/**
 * Class LogicHandler
 * @package Sws\Annotations\Handlers
 */
class LogicHandler extends AbstractHandler
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(array $classAnn, \ReflectionClass $classRef, Collector $collector)
    {
        if (!$obj = $classAnn[Logic::class] ?? null) {
            return;
        }
    }
}

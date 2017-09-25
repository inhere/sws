<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/9/25
 * Time: 下午11:46
 */

namespace Sws\Annotations\Handlers;

use Sws\Annotations\Collector;
use Sws\Annotations\AopPoint;

/**
 * Class AopPointHandler
 * @package Sws\Annotations\Handlers
 */
class AopPointHandler extends AbstractHandler
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(array $classAnn, \ReflectionClass $classRef, Collector $collector)
    {
        if ($obj = $classAnn[AopPoint::class] ?? null) {
            return;
        }
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/9/25
 * Time: 下午11:46
 */

namespace Sws\Annotations\Handlers;

use Sws\Annotations\Collector;
use Sws\Annotations\Tags\DI;

/**
 * Class DIHandler
 * @package Sws\Annotations\Handlers
 */
class DIHandler extends AbstractHandler
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(array $classAnn, \ReflectionClass $classRef, Collector $collector)
    {
        if (!$obj = $classAnn[DI::class] ?? null) {
            return;
        }
    }
}

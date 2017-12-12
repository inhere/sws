<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/12/3
 * Time: 下午11:03
 */

namespace App\Listeners;

use Inhere\Event\EventInterface;

/**
 * Class AppListener
 * @package App\Listeners
 */
class AppListener
{
    public function start(EventInterface $event)
    {
        $pos = __METHOD__;

        app('logger')->info("handle the event {$event->getName()} on the: $pos");
    }

    public function beforeRequest(EventInterface $event)
    {
        $pos = __METHOD__;
        app('logger')->info("handle the event {$event->getName()} on the: $pos");
    }

    public function afterRequest(EventInterface $event)
    {
        $pos = __METHOD__;

        app('logger')->info("handle the event {$event->getName()} on the: $pos");
    }

    public function stop(EventInterface $event)
    {
        $pos = __METHOD__;

        app('logger')->info("handle the event {$event->getName()} on the: $pos");
    }

    public static function onRuntimeEnd()
    {

    }
}

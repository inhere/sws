<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-28
 * Time: 16:50
 */

namespace Sws\Components;

use Monolog\Logger;
use Sws\SwsServer;

/**
 * Class ExtraLogger
 * @package Sws\Components
 */
class ExtraLogger extends Logger
{
    /**
     * {@inheritdoc}
     */
    public function addRecord($level, $message, array $context = [])
    {
        /** @var SwsServer $svr */
        if ($svr = \Sws::$app->get('server')) {
            $trace = [
                'workerId' => $svr->getWorkId(),
                'workerPid' => $svr->getWorkPid(),
                'isTaskWorker' => $svr->isTaskWorker(),
            ];

            if ($ctx = \Sws::getContext()) {
                $trace['ctxId'] = $ctx->getId();
                $trace['ctxKey'] = $ctx->getKey();
            }

            if (isset($context['_context'])) {
                $context['_context'] = array_merge($context['_context'], $trace);
            } else {
                $context['_context'] = $trace;
            }
        }

        return parent::addRecord($level, $message, $context);
    }
}
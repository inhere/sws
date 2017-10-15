<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/10/8
 * Time: 下午11:28
 */

namespace Sws\Components;
use Monolog\Handler\AbstractHandler;
use Monolog\Logger;
use Psr\Log\LogLevel;

/**
 * Class ErrorHandler
 * @package Sws\Components
 */
class ErrorHandler extends \Inhere\Library\Components\ErrorHandler
{
    /**
     * {@inheritDoc}
     */
    public function handleException($e)
    {
        parent::handleException($e);

        if ($ctx = \Sws::getContext()) {
            $res = \Sws::$app->handleHttpException($e, __METHOD__, $ctx);
            \Sws::$app->respondHttp($res, $ctx->getSwResponse());
        }
    }

    /**
     * @private
     */
    public function handleFatalError()
    {
        $this->reservedMemory = null;
        $lastError = error_get_last();

        if (!$lastError || !in_array($lastError['type'], self::$fatalErrors, true)) {
            return;
        }

        $digest = 'Fatal Error ('.self::codeToString($lastError['type']).'): '.$lastError['message'];

        $this->logger->log(
            $this->fatalLevel ?? LogLevel::ALERT,
            $digest,
            [
                'code' => $lastError['type'],
                'message' => $lastError['message'],
                'file' => $lastError['file'],
                'line' => $lastError['line'],
                'catcher' => __METHOD__,
            ]
        );

        if ($this->logger instanceof Logger) {
            foreach ($this->logger->getHandlers() as $handler) {
                if ($handler instanceof AbstractHandler) {
                    $handler->close();
                }
            }
        }

        if ($ctx = \Sws::getContext()) {
            \Sws::$app->endRequest($digest);
        }
    }
}

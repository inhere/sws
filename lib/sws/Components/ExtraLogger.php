<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-28
 * Time: 16:50
 */

namespace Sws\Components;

use Inhere\Console\Utils\Show;
use Inhere\Library\Helpers\PhpHelper;
use Monolog\Handler\AbstractHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Sws\AppServer;

/**
 * Class ExtraLogger
 * @package Sws\Components
 */
class ExtraLogger extends Logger
{
    /**
     * {@inheritDoc}
     */
    public function log($level, $message, array $context = array())
    {
        // translate object to string.
        if (is_object($message)) {
            $message = PhpHelper::dumpVars($message);
        }

        $svr = \Sws::$di->get('server');
        $context = $this->collectContext($context, $svr);

        if ($svr && !$svr->isDaemon()) {
            list($ts, $ms) = explode('.', sprintf('%.4f', microtime(true)));
            $ms = str_pad($ms, 4, 0);
            $time = date('Y-m-d H:i:s', $ts);
            $json = $context ? json_encode($context) : '';
            $type = Logger::getLevelName($level);

            Show::write(sprintf(
                '[%s.%s] [%s.%s] %s %s',
                $time, $ms, \Sws::$app->getName(), strtoupper($type), $message, $json
            ));
        }

        return parent::log($level, strip_tags($message), $context);
    }

    /**
     * @param array $context
     * @param AppServer $svr
     * @return array
     */
    protected function collectContext(array $context, $svr)
    {
        if ($svr) {
            $trace = [
                'workerId' => $svr->getWorkerId(),
                'workerPid' => $svr->getWorkerPid(),
                'isTaskWorker' => $svr->isTaskWorker(),
                'isUserWorker' => $svr->isUserWorker(),
                'isHttpRequest' => false,
//                'isWsRequest' => false,
            ];

            if ($ctx = \Sws::getContext()) {
                $trace['ctxId'] = $ctx->getId();
                $trace['ctxKey'] = $ctx->getKey();
                $trace['isHttpRequest'] = true;
            }

            if (isset($context['_env'])) {
                $context['_env'] = array_merge($context['_env'], $trace);
            } else {
                $context['_env'] = $trace;
            }
        }

        return $context;
    }

    /**
     * replace `call_user_func` -> PhpHelper::call()
     * {@inheritdoc}
     */
    public function addRecord($level, $message, array $context = [])
    {
        if (!$this->handlers) {
            $this->pushHandler(new StreamHandler('php://stderr', static::DEBUG));
        }

        $levelName = static::getLevelName($level);

        // check if any handler will handle this message so we can return early and save cycles
        $handlerKey = null;
        reset($this->handlers);
        while ($handler = current($this->handlers)) {
            if ($handler->isHandling(array('level' => $level))) {
                $handlerKey = key($this->handlers);
                break;
            }

            next($this->handlers);
        }

        if (null === $handlerKey) {
            return false;
        }

        if (!static::$timezone) {
            static::$timezone = new \DateTimeZone(date_default_timezone_get() ?: 'UTC');
        }

        // php7.1+ always has microseconds enabled, so we do not need this hack
        if ($this->microsecondTimestamps && PHP_VERSION_ID < 70100) {
            $ts = \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)), static::$timezone);
        } else {
            $ts = new \DateTime(null, static::$timezone);
        }
        $ts->setTimezone(static::$timezone);

        $record = [
            'message' => (string) $message,
            'context' => $context,
            'level' => $level,
            'level_name' => $levelName,
            'channel' => $this->name,
            'datetime' => $ts,
            'extra' => [],
        ];

        foreach ($this->processors as $processor) {
            $record = $processor($record);
        }

        while ($handler = current($this->handlers)) {
            if (true === $handler->handle($record)) {
                break;
            }

            next($this->handlers);
        }

        return true;
    }

    public function flush()
    {
        foreach ($this->getHandlers() as $handler) {
            if ($handler instanceof AbstractHandler) {
                $handler->close();
            }
        }
    }

    /**
     * @var array
     */
    private $profiles = [];

    /**
     * mark data analysis start
     * @param $name
     * @param array $context
     * @param string $category
     */
    public function profile($name, array $context = [], $category = 'application')
    {
        $data = [
            '_stats' => [
                'startTime' => microtime(true),
                'startMem' => memory_get_usage(),
            ],
            'start' => $context,
            'end' => null,
        ];

        $this->profiles[$category][$name] = $data;
    }

    /**
     * mark data analysis end
     * @param string $name
     * @param string|null $title
     * @param array $context
     * @param string $category
     */
    public function profileEnd($name, $title = null, array $context = [], $category = 'application')
    {
        if (isset($this->profiles[$category][$name])) {
            $data = $this->profiles[$category][$name];

            $old = $data['_stats'];
            $data['_stats'] = PhpHelper::runtime($old['startTime'], $old['startMem']);
            $data['end'] = $context;

            $title = $category . ' - ' . ($title ?: "$name");

            $this->log(self::DEBUG, $title, $data);
        }
    }

    protected function calculateConsumption($old, $now)
    {

    }

    public function getUniqueId()
    {
        // \Sws::getContext();
    }
}

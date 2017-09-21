<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/9/21
 * Time: 下午11:41
 */

namespace Sws\Components;

use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger;

/**
 * Class LoggerFactory
 * @package Sws\Components
 */
class LoggerFactory
{
    const TYPE_SINGLE= 'single';

    /**
     * @var array
     */
    private static $defaultHandlers = [
        'single' => StreamHandler::class,
        'daily' => RotatingFileHandler::class,
        'syslog' => SyslogHandler::class,
        'errorlog' => ErrorLogHandler::class,
    ];

    /**
     * @var array
     */
    private static $defaultConfig = [
        'name' => null,
        'log' => 'single', // 'single' 'daily' 'syslog' 'errorlog'

        'options' => [

        ],
    ];

    private static $allowTypes = [
        'single', 'daily', 'syslog', 'errorlog'
    ];

    /**
     * @param array $settings
     * @return Logger
     */
    public static function make(array $settings = [])
    {
        $settings = array_merge(self::$defaultConfig, $settings);

        $logger = new Logger($settings['name']);

        if (!in_array($settings['log'], self::$allowTypes, true)) {
            $settings['log'] = self::TYPE_SINGLE;
        }

        $handlerClass = self::$defaultHandlers[$settings['log']];

        $logger->pushHandler(new $handlerClass(...$settings['options']));

        return $logger;
    }
}

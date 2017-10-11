<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-31
 * Time: 9:25
 */

namespace Sws;

if (!defined('BASE_PATH')) {
    throw new \LogicException('Must be defined the constant [BASE_PATH - the project root path]');
}

use Inhere\Library\DI\Container;
use Inhere\Library\Interfaces\LanguageInterface;
use Inhere\Library\Traits\PathAliasTrait;
use Sws\Components\LogShortTrait;
use Sws\Web\ContextManager;
use Sws\Web\HttpContext;
use Sws\WebSocket\Connection;
use Sws\WebSocket\ConnectionManager;

/**
 * Class BaseSws
 * @package Sws
 */
abstract class BaseSws
{
    use PathAliasTrait;
    use LogShortTrait;

    /**
     * defined path aliases
     * @var array
     */
    protected static $aliases = [
        '@root' => BASE_PATH,
        '@app' => BASE_PATH . '/app',
        '@bin' => BASE_PATH . '/bin',
        '@config' => BASE_PATH . '/config',
        '@tmp' => BASE_PATH . '/tmp',
    ];

    /**
     * @var \Sws\ApplicationInterface|\Sws\Application
     */
    public static $app;

    /**
     * @var Container
     */
    public static $di;

    /**
     * @return \Sws\ApplicationInterface
     */
    public static function app()
    {
        return self::$app;
    }

    /**
     * @param null|string $id
     * @return Container
     */
    public static function di($id = null)
    {
        if ($id) {
            return self::$di->get($id);
        }

        return self::$di;
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function get($id)
    {
        return self::$di->get($id);
    }

    public static function getIfExist($id)
    {
        return self::$di->getIfExist($id);
    }

    /*******************************************************************************
     * some public service
     ******************************************************************************/

    /**
     * @return AppServer
     */
    public static function server()
    {
        return self::$di->get('server');
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public static function logger()
    {
        return self::$di->get('logger');
    }

    /**
     * @return LanguageInterface
     */
    public static function lang()
    {
        return self::$di->get('lang');
    }

    /**
     * @param string $key
     * @param array $args
     * @param null $lang
     * @return array|string
     */
    public static function tl($key, array $args = [], $lang = null)
    {
        return self::$di->get('lang')->tl($key, $args, $lang);
    }

    /**
     * {@inheritDoc}
     */
    public static function log($level, $message, array $context = [])
    {
        self::$di->get('logger')->log($level, $message, $context);
    }

    /*******************************************************************************
     * http context
     ******************************************************************************/

    /**
     * @return ContextManager
     */
    public static function getContextManager()
    {
        return self::$di->get('ctxManager');
    }

    /**
     * @param null|int $id
     * @return HttpContext
     */
    public static function getContext($id = null)
    {
        return self::$di->get('ctxManager')->get($id);
    }

    /**
     * @return \Inhere\Http\Request
     */
    public static function getRequest()
    {
        return self::$di->get('ctxManager')->getRequest();
    }

    /**
     * @return \Inhere\Http\Response
     */
    public static function getResponse()
    {
        return self::$di->get('ctxManager')->getResponse();
    }

    /*******************************************************************************
     * websocket context
     ******************************************************************************/

    /**
     * @return ConnectionManager
     */
    public static function getConnectionManager()
    {
        return self::$di->get('cnnManager');
    }

    /**
     * @param int $id \Swoole\Http\Request->fd
     * @return Connection
     */
    public static function getConnection($id)
    {
        return self::$di->get('cnnManager')->get($id);
    }

}

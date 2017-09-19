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

use inhere\library\di\Container;
use inhere\library\interfaces\LanguageInterface;
use inhere\library\traits\PathAliasTrait;
use Sws\Context\ContextManager;

/**
 * Class BaseSws
 * @package Sws
 */
abstract class BaseSws
{
    use PathAliasTrait;

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
     * @var \Sws\ApplicationInterface
     */
    public static $app;

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
            return self::$app->get($id);
        }

        return self::$app->getDi();
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function get($id)
    {
        return self::$app->get($id);
    }

    public static function getIfExist($id)
    {
        return self::$app->getDi()->getIfExist($id);
    }

    /*******************************************************************************
     * some public service
     ******************************************************************************/

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public static function logger()
    {
        return self::$app->get('logger');
    }

    /**
     * @return LanguageInterface
     */
    public static function lang()
    {
        return self::$app->get('lang');
    }

    /*******************************************************************************
     * request context
     ******************************************************************************/

    /**
     * @param null|string|int $id
     * @return null|Context\ContextInterface
     */
    public static function getContext($id = null)
    {
        return ContextManager::getContext($id);
    }

    /**
     * @param null|string|int $id
     * @return \Inhere\Http\Request|null
     */
    public static function getRequest($id = null)
    {
        return ContextManager::getRequest($id);
    }

    /**
     * @param null|string|int $id
     * @return \Inhere\Http\Response|null
     */
    public static function getResponse($id = null)
    {
        return ContextManager::getResponse($id);
    }
}

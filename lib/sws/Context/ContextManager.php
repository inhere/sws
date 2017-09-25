<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-31
 * Time: 15:17
 */

namespace Sws\Context;

use Inhere\Http\Request;
use Inhere\Http\Response;
use Sws\Coroutine\Coroutine;

/**
 * Class ContextManager
 * @package Sws\Context
 */
class ContextManager
{
    /**
     * @var ContextInterface[]
     * [
     *  id => Context
     * ]
     */
    private static $contextMap = [];

    /**
     * @return int
     */
    public static function count()
    {
        return count(self::$contextMap);
    }

    /**
     * @param null|int|string $id
     * @param bool $thrError
     * @return null|Request
     */
    public static function getRequest($id = null, $thrError = true)
    {
        $id = $id ?: Coroutine::tid();

        if ($ctx = self::getContext($id)) {
            return $ctx->getRequest();
        }

        if ($thrError) {
            throw new \RuntimeException("the request context is not exists for CoId:[$id]");
        }

        return null;
    }

    /**
     * @param null|int|string $id
     * @param bool $thrError
     * @return null|Response
     */
    public static function getResponse($id = null, $thrError = true)
    {
        $id = $id ?: Coroutine::tid();

        if ($ctx = self::getContext($id)) {
            return $ctx->getResponse();
        }

        if ($thrError) {
            throw new \RuntimeException("the request context is not exists for CoId:[$id]");
        }

        return null;
    }

    /**
     * @param string $id
     * @return bool
     */
    public static function hasContext($id)
    {
        return isset(self::$contextMap[$id]);
    }

    /**
     * @param ContextInterface $context
     */
    public static function addContext(ContextInterface $context)
    {
        self::$contextMap[$context->getId()] = $context;
    }

    /**
     * @param string|int $id
     * @return ContextInterface|null
     */
    public static function getContext($id = null)
    {
        $id = $id ?: Coroutine::tid();

        return self::$contextMap[$id] ?? null;
    }

    /**
     * @param int|string|ContextInterface $id
     * @return ContextInterface|null
     */
    public static function delContext($id = null)
    {
        $ctx = null;
        $id = $id ?: Coroutine::tid();

        if ($id instanceof ContextInterface) {
            $id = $id->getId();
        }

        if ($ctx = self::getContext($id)) {
            unset(self::$contextMap[$id]);
        }

        return $ctx;
    }

    /**
     * @return array
     */
    public static function getContextMap(): array
    {
        return self::$contextMap;
    }

    /**
     * @param array $contextMap
     */
    public static function setContextMap(array $contextMap)
    {
        self::$contextMap = $contextMap;
    }
}

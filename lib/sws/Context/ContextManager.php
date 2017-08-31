<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-31
 * Time: 15:17
 */

namespace Sws\Context;

use inhere\server\helpers\ServerHelper;

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
     * @param string $id
     * @return ContextInterface|null
     */
    public static function getContext($id)
    {
        return self::$contextMap[$id] ?? null;
    }

    /**
     * @param string|ContextInterface $id
     * @return ContextInterface|null
     */
    public static function delContext($id)
    {
        if (!$id) {
            return null;
        }

        $ctx = null;

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

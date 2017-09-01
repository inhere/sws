<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-31
 * Time: 15:17
 */

namespace Sws\Context;

/**
 * Class ContextManagerTrait
 * @package Sws\Context
 * 
 * @property array $contextMap
 * [
 *  id => ContextInterface
 * ]
 */
trait ContextManagerTrait
{
    /**
     * @var ContextInterface[]
     * [
     *  id => Context
     * ]
     */
//    protected static $contextMap = [];

    /**
     * @return int
     */
    public static function count()
    {
        return count(static::$contextMap);
    }

    /**
     * @param string $id
     * @return bool
     */
    public static function hasContext($id)
    {
        return isset(static::$contextMap[$id]);
    }

    /**
     * @param ContextInterface $context
     */
    public static function addContext(ContextInterface $context)
    {
        static::$contextMap[$context->getId()] = $context;
    }

    /**
     * @param string $id
     * @return ContextInterface|null
     */
    public static function getContext($id)
    {
        return static::$contextMap[$id] ?? null;
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

        if ($ctx = static::getContext($id)) {
            unset(static::$contextMap[$id]);
        }

        return $ctx;
    }

    /**
     * @return array
     */
    public static function getContextMap(): array
    {
        return static::$contextMap;
    }

    /**
     * @param array $contextMap
     */
    public static function setContextMap(array $contextMap)
    {
        static::$contextMap = $contextMap;
    }
}

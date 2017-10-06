<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-30
 * Time: 9:24
 */

namespace Sws\Context;

/**
 * Class ContextStaticGetTrait
 * @package Sws\Context
 */
trait ContextStaticGetTrait
{
    /**
     * @return ContextInterface
     */
    public static function getContext()
    {
        return ContextManager::getContext();
    }

    /**
     * @return \Inhere\Http\Request
     */
    public static function getRequest()
    {
        return ContextManager::getRequest();
    }

    /**
     * @return \Inhere\Http\Response
     */
    public static function getResponse()
    {
        return ContextManager::getResponse();
    }
}

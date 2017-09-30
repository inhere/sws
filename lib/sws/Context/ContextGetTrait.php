<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-30
 * Time: 9:24
 */

namespace Sws\Context;

/**
 * Class ContextGetTrait
 * @package Sws\Context
 */
trait ContextGetTrait
{
    /**
     * @return ContextInterface
     */
    public function getContext()
    {
        return ContextManager::getContext();
    }

    /**
     * @return \Inhere\Http\Request
     */
    public function getRequest()
    {
        return ContextManager::getRequest();
    }

    /**
     * @return \Inhere\Http\Response
     */
    public function getResponse()
    {
        return ContextManager::getResponse();
    }
}
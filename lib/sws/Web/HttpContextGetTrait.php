<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-30
 * Time: 9:24
 */

namespace Sws\Web;

use Sws\Context\ContextInterface;

/**
 * Class HttpContextGetTrait
 * @package Sws\Web
 */
trait HttpContextGetTrait
{
    /**
     * @return ContextInterface
     */
    public function getContext()
    {
        return \Sws::getContext();
    }

    /**
     * @return \Inhere\Http\Request
     */
    public function getRequest()
    {
        return \Sws::getRequest();
    }

    /**
     * @return \Inhere\Http\Response
     */
    public function getResponse()
    {
        return \Sws::getResponse();
    }
}
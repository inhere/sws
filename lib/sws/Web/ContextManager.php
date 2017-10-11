<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-10-11
 * Time: 11:03
 */

namespace Sws\Web;

use Sws\Coroutine\Coroutine;

/**
 * Class ContextManager
 * @package Sws\Web
 */
class ContextManager extends \Sws\Context\ContextManager
{
    /**
     * @return int|string
     */
    protected function getDefaultId()
    {
        return Coroutine::tid();
    }
}
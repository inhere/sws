<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-29
 * Time: 15:46
 */

namespace Sws\Web;

use Sws\Context\HttpContext;

/**
 * Class RestController
 * @package Sws\Web
 */
class RestController
{
    public function optionsAction(HttpContext $ctx)
    {
        $allow = ['HEAD','GET','PUT','POST','DELETE','OPTIONS'];
        $ctx->getResponse()->setHeader('Allow', implode(',', $allow));
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-29
 * Time: 15:46
 */

namespace Sws\Web;

use Inhere\Http\Request;
use Inhere\Http\Response;
use Sws\Context\ContextGetTrait;

/**
 * Class BaseController
 * @package Sws\Web
 */
abstract class BaseController
{
    use ContextGetTrait;

    /**
     * @param Request $req
     * @return bool
     */
    public function isAjax(Request $req = null)
    {
        $req = $req ?: $this->getRequest();

        return $req->isXhr();
    }

    /**
     * @param string $url
     * @param int $status
     * @param Response $response
     * @return mixed
     */
    public function redirect($url, $status = 302, $response = null)
    {
        $response = $response ?: $this->getResponse();

        $response->setStatus((int)$status);
        $response->setHeader('Location', $url);

        return $response;
    }

}
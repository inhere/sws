<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-31
 * Time: 15:19
 */

namespace Sws\Context;

use Inhere\Http\ServerRequest as Request;
use Inhere\Http\Response;
//use Psr\Http\Message\ResponseInterface;
//use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface ContextInterface
 * @package Sws\Context
 *
 * @property string $id The request context unique ID
 */
interface ContextInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @param  string $id
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getKey();

    /**
     * destroy something ...
     */
    public function destroy();

    /**
     * @return Request
     */
    public function getRequest();

    /**
     * @return Response
     */
    public function getResponse();

    /**
     * @return array
     */
    public function getArgs(): array;

    /**
     * @param array $args
     */
    public function setArgs(array $args);
}

<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/10/16
 * Time: 下午11:07
 */

namespace Inhere\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class RequestHandler
 * @package Inhere\Middleware
 */
class RequestHandler implements RequestHandlerInterface
{
    protected $responseFactory;

    /** @var MiddlewareInterface[] */
    protected $middlewares;

    public function __construct(MiddlewareInterface ...$middlewares)
    {
        $this->middlewares = $middlewares;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $handler = clone $this;

        if (null === key($handler->middlewares)) {
            return null;
//            return $this->responseFactory->createResponse();
        }

        $middleware = current($handler->middlewares);
        next($handler->middlewares);

        $response = $middleware->process($request, $handler);

        if (!$response instanceof ResponseInterface) {
            throw new \HttpInvalidParamException('error response');
        }

        return $response;
    }
}

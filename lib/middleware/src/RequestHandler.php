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
    /** @var ResponseInterface */
    protected $response;

    protected $responseFactory;

    /** @var MiddlewareInterface[] */
    protected $middlewares;

    /**
     * RequestHandler constructor.
     * @param MiddlewareInterface[] ...$middlewares
     */
    public function __construct(...$middlewares)
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
//            return $this->responseFactory->createResponse();
            return $this->response;
        }

        $response = $this->response;
        $middleware = current($handler->middlewares);
        next($handler->middlewares);

        if (method_exists($middleware, '__invoke')) {
            $response = $middleware($request, $handler);
        } elseif ($middleware instanceof MiddlewareInterface) {
            $response = $middleware->process($request, $handler);
        }

        if (!$response instanceof ResponseInterface) {
            throw new \HttpInvalidParamException('error response');
        }

        return $response;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @param ResponseInterface $response
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
    }

}

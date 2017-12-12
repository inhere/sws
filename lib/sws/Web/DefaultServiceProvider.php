<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-10-18
 * Time: 18:58
 */
namespace Sws\Web;

use Inhere\Http\Headers;
use Inhere\Http\HttpFactory;
use Inhere\Http\Response;
use Inhere\Library\DI\Container;
use Inhere\Library\DI\ServiceProviderInterface;
use Inhere\Library\Web\Environment;
use Inhere\Library\DI\CallableResolver;
use Inhere\Middleware\CallableResolverInterface;
use Inhere\Web\Handlers\ErrorRenderer;
use Inhere\Web\Handlers\NotAllowed;
use Inhere\Web\Handlers\NotFound;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * the app's default Service Provider.
 */
class DefaultServiceProvider implements ServiceProviderInterface
{
    /**
     * Register default services.
     *
     * @param Container $di A DI container implementing ArrayAccess and ContainerInterface.
     */
    public function register(Container $di)
    {
        if (IN_SWOOLE) {
            $this->registerServicesForSwoole($di);
        } else {
            $this->registerServicesForCGI($di);
        }

        if (!isset($di['httpDispatcher'])) {
            /**
             * This service MUST return a SHARED instance
             * @return HttpDispatcher
             */
            $di['routeDispatcher'] = function () {
                return new HttpDispatcher();
            };
        }

        if (!isset($di['errorHandler'])) {
            /**
             * This service MUST return a callable
             * that accepts three arguments:
             * 1. Instance of \Psr\Http\Message\ServerRequestInterface
             * 2. Instance of \Psr\Http\Message\ResponseInterface
             * 3. Instance of \Exception
             * The callable MUST return an instance of
             * \Psr\Http\Message\ResponseInterface.
             * @param Container $di
             * @return callable
             */
            $di['errorHandler'] = function ($di) {
                return new ErrorRenderer($di->get('config')['displayErrorDetails'], $di->get('logger'));
            };
        }

        if (!isset($di['notFoundHandler'])) {
            /**
             * This service MUST return a callable
             * that accepts two arguments:
             *  1. Instance of \Psr\Http\Message\ServerRequestInterface
             *  2. Instance of \Psr\Http\Message\ResponseInterface
             * The callable MUST return an instance of
             * \Psr\Http\Message\ResponseInterface.
             * @return callable
             */
            $di['notFoundHandler'] = function () {
                return new NotFound;
            };
        }

        if (!isset($di['notAllowedHandler'])) {
            /**
             * This service MUST return a callable
             * that accepts three arguments:
             * 1. Instance of \Psr\Http\Message\ServerRequestInterface
             * 2. Instance of \Psr\Http\Message\ResponseInterface
             * 3. Array of allowed HTTP methods
             * The callable MUST return an instance of
             * \Psr\Http\Message\ResponseInterface.
             * @return callable
             */
            $di['notAllowedHandler'] = function () {
                return new NotAllowed;
            };
        }

        if (!isset($di['callableResolver'])) {
            /**
             * Instance of CallableResolverInterface
             * @param Container $di
             * @return CallableResolverInterface
             */
            $di['callableResolver'] = function ($di) {
                return new CallableResolver($di);
            };
        }
    }

    protected function registerServicesForCGI(Container $di)
    {
        if (!isset($di['environment'])) {
            /**
             * This service MUST return a shared instance
             * of \Slim\Interfaces\Http\EnvironmentInterface.
             * @return Environment
             */
            $di['environment'] = function () {
                return new Environment($_SERVER);
            };
        }

        if (!isset($di['request'])) {
            /**
             * PSR-7 Request object
             * @param Container $di
             * @return ServerRequestInterface
             */
            $di['request'] = function ($di) {
                return HttpFactory::createServerRequestFromArray($di->get('environment'));
            };
        }

        if (!isset($di['response'])) {
            /**
             * PSR-7 Response object
             * @param Container $di
             * @return ResponseInterface
             */
            $di['response'] = function ($di) {
                $headers = new Headers(['Content-Type' => 'text/html; charset=UTF-8']);
                $response = new Response(200, $headers);

                return $response->withProtocolVersion($di->get('config')->get('response.httpVersion'));
            };
        }
    }

    protected function registerServicesForSwoole(Container $di)
    {

    }
}

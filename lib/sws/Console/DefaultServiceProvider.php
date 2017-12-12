<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-10-18
 * Time: 18:58
 */
namespace Mco\Console;

use Inhere\Console\IO\Input;
use Inhere\Console\IO\InputInterface;
use Inhere\Console\IO\Output;
use Inhere\Console\IO\OutputInterface;
use Inhere\Library\DI\Container;
use Inhere\Library\DI\ServiceProviderInterface;
use Inhere\Library\DI\CallableResolver;
use Inhere\Middleware\CallableResolverInterface;
use Sws\Web\Handlers\ErrorRenderer;

/**
 * the default Service Provider.
 */
class DefaultServiceProvider implements ServiceProviderInterface
{
    /**
     * Register default services.
     *
     * @param Container $di A DI container implementing ArrayAccess and ContainerInterface
     */
    public function register(Container $di)
    {
        if (!isset($di['input'])) {
            /**
             * Console input object
             * @return InputInterface
             */
            $di['input'] = function () {
                return new Input();
            };
        }

        if (!isset($di['response'])) {
            /**
             * Console output object
             * @return OutputInterface
             */
            $di['output'] = function () {
                return new Output();
            };
        }

        if (!isset($di['errorHandler'])) {
            /**
             * This service MUST return a callable
             * that accepts three arguments:
             * - Instance of \Exception
             * The callable MUST return an instance of
             * \Psr\Http\Message\ResponseInterface.
             * @param Container $di
             * @return callable
             */
            $di['errorHandler'] = function ($di) {
                return new ErrorRenderer($di->get('config')['debug'], $di->get('logger'));
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
}

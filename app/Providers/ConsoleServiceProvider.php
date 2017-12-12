<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/11/27
 * Time: 下午11:53
 */

namespace App\Providers;

use Inhere\Library\Collections\Configuration;
use Inhere\Library\DI\Container;
use Inhere\Library\DI\ServiceProviderInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Sws\Console\Application;

/**
 * Class ConsoleServiceProvider
 * @package App\Providers
 */
class ConsoleServiceProvider implements ServiceProviderInterface
{
    /**
     * 注册一项服务(可能含有多个服务)提供者到容器中
     * @param Container $di
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \RangeException
     */
    public function register(Container $di)
    {
        /** @var Configuration $config */
        $config = $di->get('config');

        $config->load(include BASE_PATH . '/conf/console.php');

        // current env config file. e.g '/config/console/dev.php'
        $envFile = \Sws\path('@conf/console/' . APP_ENV. '.php');

        if (is_readable($envFile)) {
            $config->load(include $envFile);
        }

        // load services from config
        $di->sets($config->remove('services'));

        $di->set('logger', function (Container $di) {
            $settings = $di->get('config')->get('logger', []);

            $logger = new Logger($settings['name']);
            // $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
            $logger->pushHandler(new StreamHandler($settings['file'], (int)$settings['level']));

            return $logger;
        });

        // monolog - database logger
        $di['dbLogger'] = function (Container $c) {
            $settings = $c->get('config')->get('dbLogger', []);

            $logger = new Logger($settings['name']);
            // $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
            $handler = new StreamHandler($settings['path'], Logger::DEBUG);

            // formatter, ordering log rows
            $handler->setFormatter(new LineFormatter("[%datetime%] SQL: %message% \n"));
            $logger->pushHandler($handler);

            return $logger;
        };

        $di->set('app', function (Container $di) {
            $settings = $di->get('config')->get('application', []);
            $app = new Application($settings);
            // $app->setDi($di);

            // register commands
            require \Sws\path('@app/Console/routes.php');

            return $app;
        });
    }
}

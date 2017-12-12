<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/11/27
 * Time: 下午11:51
 */

namespace App\Providers;

use Inhere\Library\Collections\Configuration;
use Inhere\Library\DI\Container;
use Inhere\Library\DI\ServiceProviderInterface;
use Inhere\Route\ORouter;
use Monolog\Handler\FingersCrossedHandler;
use Sws\Application;
use Sws\AppServer;
use Sws\Components\AppLogHandler;
use Sws\Components\ExtraLogger;

/**
 * Class WebServiceProvider
 * @package App\Providers
 */
class WebServiceProvider implements ServiceProviderInterface
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

        $config->load(include BASE_PATH . '/conf/web.php');

        // current env config file. e.g '/config/web/dev.php'
        $envFile = \Sws\path('@conf/web/' . APP_ENV. '.php');

        if (is_readable($envFile)) {
            $config->load(include $envFile);
        }

        // load services from config
        $di->sets($config->remove('services'));

        $this->loadWebRoutes($di->get('router'));

        // ...

        $di->set('logger', function (Container $di) {
            $opts = $di->get('config')->get('logger', []);

            $file = \Sws::alias($opts['file']);
            $fileHandler = new AppLogHandler($file, (int)$opts['level'], (int)$opts['splitType']);
            $mainHandler = new FingersCrossedHandler($fileHandler, (int)$opts['level'], $opts['bufferSize']);

            $logger = new ExtraLogger($opts['name']);
            // $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
            $logger->pushHandler($mainHandler);

            return $logger;
        });

        $di->set('server', function (Container $di) {
            $config = require \Sws\path('@conf/server.php');
            // $sever = new SwsServer($config);

            return new AppServer($config);
        });

        $di->set('app', function (Container $di) {
            $opts = $di->get('config')->get('application', []);

            $app = new Application($opts);
            $app->setServer($di->get('server'));
            // $app->setDi($di);

            return $app;
        });
    }

    /**
     * @param ORouter $router
     */
    private function loadWebRoutes(ORouter $router)
    {
        include BASE_PATH . '/app/Http/routes.php';
    }
}

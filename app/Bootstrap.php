<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/11/27
 * Time: 上午12:18
 */

namespace App;

use App\Listeners\AppListener;
use App\Providers\CommonServiceProvider;
use App\Providers\ConsoleServiceProvider;
use App\Providers\WebServiceProvider;
use Inhere\Library\Components\EnvDetector;
use \Inhere\Library\DI\Container;
use Inhere\Library\Components\PhpDotEnv;
use Inhere\Library\DI\ContainerManager;
use Sws\Application as WebApp;
use Sws\Console\Application as CliApp;

/**
 * Class Bootstrap
 * @package App
 */
class Bootstrap
{
    /**
     * for swoole app
     */
    const SWOOLE_EVENTS = [
        'start' => [

        ],
    ];

    /**
     * for swoole app
     */
    const ACTIVE_SERVICES = [
        'logger', 'httpRouter',
    ];

    /**
     * @param Container $di
     * @return CliApp|WebApp
     */
    public static function boot(Container $di = null)
    {
        if (!$di) {
            $di = ContainerManager::make();
        }

        \Sws::$di = $di;

        return (new self)->run($di);
    }

    protected function prepare()
    {
        // init .env
        PhpDotEnv::load(BASE_PATH);
        // init env setting
        EnvDetector::setHost2env(HOST2ENV);
        EnvDetector::setDomain2env(DOMAIN2ENV);

        // define current is IN_CODE_TESTING.
        if (!\defined('IN_CODE_TESTING')) {
            \define('IN_CODE_TESTING', false);
        }

        date_default_timezone_set(env('TIMEZONE', 'UTC'));
    }

    /**
     * @param Container $di
     * @return CliApp|WebApp
     * @throws \InvalidArgumentException
     */
    protected function run(Container $di)
    {
        $this->prepare();

        // Read common services
        $di->registerServiceProvider(new CommonServiceProvider());

        if (RUN_MODE === 'web') {
            $app = $this->loadWebServices($di);
        } else {
            $app = $this->loadCliServices($di);
        }

        $this->init($di);

        return $app;
    }

    /**
     * @param Container $di
     */
    public function init(Container $di)
    {
        // date timezone
//        date_default_timezone_set($config->get('timezone', 'UTC'));
        \define('APP_DEBUG', $di['config']->get('debug'));

        switch (APP_ENV) {
            case APP_DEV:
            case APP_TEST:
                ini_set('display_errors', 1);
                ini_set('display_startup_errors', 1);
                error_reporting(-1);
                break;
            default:
                ini_set('display_errors', 0);
                ini_set('display_startup_errors', 0);
                error_reporting(0);
                break;
        }

        if (PHP_SAPI === 'cli') {
            ini_set('html_errors', 0);
        } else {
            ini_set('html_errors', 1);
        }

        // Set the MB extension encoding to the same character set
        if (\function_exists('mb_internal_encoding')) {
            mb_internal_encoding('utf-8');
        }

        // on runtime end.
        register_shutdown_function(AppListener::class . '::onRuntimeEnd', $di);
    }

    /**
     * @param Container $di
     * @return WebApp
     * @throws \InvalidArgumentException
     */
    public function loadWebServices(Container $di)
    {
        // Detect environment: allow change env by HOSTNAME OR HTTP_HOST
        if (!$envName = env('APP_ENV')) {
            $envName = EnvDetector::getEnvNameByHost() ?: EnvDetector::getEnvNameByDomain(APP_PDT);
        }

        // APP_ENV Current application environment
        if (!\defined('APP_ENV')) {
            \define('APP_ENV', $envName);
        }

        // Some services for WEB
        $di->registerServiceProvider(new WebServiceProvider());

        $em = $di->get('eventManager');
        $em->attach('app', new AppListener());

        $app = $di->get('app');

        return $app;
    }

    /**
     * @param Container $di
     * @return CliApp
     */
    public function loadCliServices(Container $di)
    {
        // Detect environment: allow change env by HOSTNAME
        if (!$envName = env('APP_ENV')) {
            $envName = EnvDetector::getEnvNameByHost(APP_PDT);
        }

        // APP_ENV Current application environment
        if (!\defined('APP_ENV')) {
            \define('APP_ENV', $envName);
        }

        // some services for CLI
        $di->registerServiceProvider(new ConsoleServiceProvider());

        $em = $di->get('eventManager');
        $em->attach('app', new AppListener());

        $app = $di->get('app');
        // $app->setEventManager($em);

        return $app;
    }
}

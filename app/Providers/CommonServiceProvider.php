<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/11/27
 * Time: 下午11:53
 */

namespace App\Providers;

use App\Listeners\MysqlListener;
use Inhere\Event\EventManager;
use Inhere\Library\Collections\Configuration;
use Inhere\Library\Components\AopProxy;
use Inhere\Library\Components\DatabaseClient;
use Inhere\Library\Components\MemcacheClient;
use Inhere\Library\Components\RedisClient;
use Inhere\Library\DI\Container;
use Inhere\Library\DI\ServiceProviderInterface;

/**
 * Class CommonServiceProvider
 * @package App\Providers
 */
class CommonServiceProvider implements ServiceProviderInterface
{
    /**
     * 注册一项服务(可能含有多个服务)提供者到容器中
     * @param Container $di
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function register(Container $di)
    {
        // Register the global configuration as config
        $di->set('config', function () {
            // load basic common config data.
            return new Configuration(include BASE_PATH . '/conf/_base.php');
        });

        $di->set('aop', function () {
            return new AopProxy();
        });

        $di->set('eventManager', function () {
            return new EventManager();
        });

        // database services.
        $this->registerDbServices($di);

        // cache services.
        $this->registerCacheServices($di);
    }

    private function registerDbServices(Container $di)
    {
        // MySQL: Database connection
        $di->set('mainMysql.master', function (Container $di) {
            $em = $di->get('eventManager');
            $em->attach('db', new MysqlListener([
                'service' => 'mainMysql.master'
            ]));
            $config = $di->get('config')->get('mainMysql.master');

            return new DatabaseClient($config);
        });

        $di->set('mainMysql.slave', function (Container $di) {
            $em = $di->get('eventManager');
            $em->attach('db', new MysqlListener([
                'role' => 'slave',
                'service' => 'mainMysql.slave'
            ]));

            $config = $di->get('config')->get('mainMysql.slave');
            // $db->setEventManager($em);

            return new DatabaseClient($config);
        });

        // Mongo: Connecting to Mongo
        $di->set('mainMongo', function (Container $di) {
            $config = $di->get('config')->get('mainMongo');

            // 'mongodb:///tmp/mongodb-27017.sock,localhost:27017'
            $mongo = new \MongoClient('mongodb://' . $config['server'], $config['options']);

            return $mongo->selectDB($config['dbname']);
        });
    }

    private function registerCacheServices(Container $di)
    {
        $di->set('memcache', function (Container $di) {
            $config = $di->get('config')->get('memcache');

            return new MemcacheClient($config);
        });

        // $di->set('cacheRedis', function (Container $di) {
        //     $config = $di->get('config')->get('cacheRedis');
        //
        //     return new RedisClient($config);
        // });

        $di->set('dataRedis', function (Container $di) {
            $config = $di->get('config')->get('dataRedis');

            return new RedisClient($config);
        });
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-08
 * Time: 15:11
 */

namespace Sws\Components;

use Inhere\Pool\Swoole\ResourcePool;
use Swoole\Coroutine;
use Swoole\Coroutine\MySQL;

/**
 * Class CoroMysqlPool
 * @package Sws\Components
 */
class CoroMysqlPool extends ResourcePool
{
    /**
     * @var array
     */
    protected $options = [
        'db1' => [
            'host' => 'mysql',
            'port' => 3306,
            'user' => 'root',
            'password' => 'password',
            'database' => 'my_test',
        ]
    ];

    /**
     * 创建新的资源实例
     * @return mixed
     */
    public function create()
    {
        $conf = $this->options['db1'];
        $db = new MySQL();

//        debug('coId:' . Coroutine::getuid() . ' will create new db connection');

        $db->connect($conf);

//        debug('coId:' . Coroutine::getuid() . ' a new db connection created');

        return $db;
    }

    /**
     * 销毁资源实例
     * @param $resource
     * @return void
     */
    public function destroy($resource)
    {
//        unset($resource);
    }
}


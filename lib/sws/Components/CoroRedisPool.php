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
use Swoole\Coroutine\Redis;

/**
 * Class CoroRedisPool
 * @package Sws\Components
 */
class CoroRedisPool extends ResourcePool
{
    /**
     * 创建新的资源实例
     * @return mixed
     */
    public function create()
    {
        $rds = new Redis();

//        debug('coId:' . Coroutine::getuid() . ' will create new redis connection');

        $rds->connect('redis', 6379);

//        debug('coId:' . Coroutine::getuid() . ' a new redis connection created');

        return $rds;
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


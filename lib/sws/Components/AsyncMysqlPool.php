<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-08
 * Time: 16:30
 */

namespace Sws\Components;

use Inhere\Pool\Swoole\ResourcePool;
use Swoole\MySQL;

/**
 * Class AsyncMysqlPool
 * @package Sws\Components
 */
class AsyncMysqlPool extends ResourcePool
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
            'charset' => 'utf8', //指定字符集
            'timeout' => 2,  // 可选：连接超时时间（非查询超时时间），默认为SW_MYSQL_CONNECT_TIMEOUT（1.0）
        ]
    ];

    /**
     * 创建新的资源实例
     * @return mixed
     */
    public function create()
    {
        $db = new MySQL();
        $config = $this->options['db1'];

        $db->connect($config, function (MySQL $db, $r) {
            if ($r === false) {
                var_dump($db->connect_errno, $db->connect_error);
                throw new \RuntimeException('connect to mysql server failed');
            }

//            $sql = 'show tables';
//            $db->query($sql, function (MySQL $db, $r) {
//                if ($r === false) {
//                    var_dump($db->error, $db->errno);
//                } elseif ($r === true) {
//                    var_dump($db->affected_rows, $db->insert_id);
//                }
//                var_dump($r);
//                $db->close();
//            });

            yield $db;
        });

        return $db;
    }

    /**
     * 销毁资源实例
     * @param $resource
     * @return void
     */
    public function destroy($resource)
    {
        $resource->close();
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }
}
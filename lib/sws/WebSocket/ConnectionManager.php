<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/9/3
 * Time: 上午1:09
 */

namespace Sws\WebSocket;

/**
 * Class ConnectionManager
 * @package Sws\WebSocket
 */
class ConnectionManager
{
    /**
     * all connections
     * @var Connection[]
     * [
     *   id => Connection
     * ]
     */
    private static $connections = [];

    /**
     * @param $id
     * @return mixed|null
     */
    public static function get($id)
    {
        return self::$connections[$id] ?? null;
    }

    /**
     * @param $id
     * @param Connection $connection
     */
    public static function add($id, Connection $connection)
    {
        self::$connections[$id] = $connection;
    }

    /**
     * @param $id
     * @return mixed|null
     */
    public static function del($id)
    {
        if ($conn = self::$connections[$id] ?? null) {
            unset(self::$connections[$id]);
        }

        return $conn;
    }

    /**
     * @return int
     */
    public static function count()
    {
        return count(self::$connections);
    }

    /**
     * @return array
     */
    public static function getConnections(): array
    {
        return self::$connections;
    }

    /**
     * @param array $connections
     */
    public static function setConnections(array $connections)
    {
        self::$connections = $connections;
    }
}

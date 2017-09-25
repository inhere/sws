<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-25
 * Time: 18:12
 */

namespace Sws\Cache;

/**
 * Interface CacheInterface
 * @package Sws\Cache
 */
interface CacheInterface
{
    public static function __callStatic($method, array $args = []);
}
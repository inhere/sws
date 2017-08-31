<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-31
 * Time: 9:25
 */

namespace Sws;

if (!defined('BASE_PATH')) {
    throw new \LogicException('Must be defined the constant [BASE_PATH - the project root path]');
}

use inhere\library\traits\PathAliasTrait;

/**
 * Class BaseSws
 * @package Sws
 */
abstract class BaseSws
{
    use PathAliasTrait;

    /**
     * defined path aliases
     * @var array
     */
    protected static $aliases = [
        '@root' => BASE_PATH,
        '@app' => BASE_PATH . '/app',
        '@bin' => BASE_PATH . '/bin',
        '@config' => BASE_PATH . '/config',
        '@tmp' => BASE_PATH . '/tmp',
    ];
}
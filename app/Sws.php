<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-25
 * Time: 9:23
 */

use inhere\library\traits\PathAliasTrait;

/**
 * Class Sws
 */
class Sws
{
    use PathAliasTrait;

    /**
     * @var \app\cli\App|\sws\App
     */
    public static $app;

    /**
     * @return \app\cli\App|\sws\App
     */
    public static function app()
    {
        return self::$app;
    }
}
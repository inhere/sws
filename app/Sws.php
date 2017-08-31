<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-25
 * Time: 9:23
 */

use Sws\BaseSws;

/**
 * Class Sws
 */
class Sws extends BaseSws
{
    /**
     * @var \app\cli\App|\Sws\App
     */
    public static $app;

    /**
     * @return \app\cli\App|\Sws\App
     */
    public static function app()
    {
        return self::$app;
    }
}
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
     * @var \App\Cli\Application|\Sws\Application
     */
    public static $app;

    /**
     * @return \App\Cli\Application|\Sws\Application
     */
    public static function app()
    {
        return self::$app;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/10/16
 * Time: 下午11:51
 */

error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('Asia/Shanghai');

spl_autoload_register(function ($class) {
    if (0 === strpos($class,'Inhere\Middleware\Examples\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Inhere\Middleware\Examples\\')));
        $file =__DIR__ . "/{$path}.php";

        if (is_file($file)) {
            include $file;
        }

    } elseif (0 === strpos($class,'Inhere\Middleware\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('Inhere\Middleware\\')));
        $file = dirname(__DIR__) . "/src/{$path}.php";

        if (is_file($file)) {
            include $file;
        }
    }
});

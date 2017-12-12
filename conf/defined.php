<?php
/**
 * common constants
 */

// Define some useful constants
define('BASE_PATH',  dirname(__DIR__));
define('APP_PATH',  BASE_PATH . '/app');

/** Env list */
define('APP_PDT', 'pdt');
define('APP_PRE', 'pre');
define('APP_TEST', 'test');
define('APP_DEV', 'dev');

// @const APP_START_TIME The start time of the application, used for profiling
define('APP_START_TIME', microtime(true));

// @const APP_START_MEMORY The memory usage at the start of the application, used for profiling
define('APP_START_MEMORY', memory_get_usage());

// @const HOSTNAME Current hostname
define('HOSTNAME', explode('.', gethostname())[0]);

// Env Detector settings

define('HOST2ENV', [
    // host keywords => env name
    'InhereMac' => 'dev',
]);

define('DOMAIN2ENV', [
    // domain keywords => env name
    'pre' => 'pre',
    'test' => 'test',
    '127.0.0.1' => 'dev',
    'dev' => 'dev'
]);

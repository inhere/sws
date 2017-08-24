<?php

use inhere\library\collections\Collection;

// composer auto loader
require BASE_PATH . '/vendor/autoload.php';

error_reporting(E_ALL);

$local = [
    'env' => 'pdt',
    'rootPath' => BASE_PATH
];
$locFile = BASE_PATH . '/.local';

if (is_file($locFile)) {
    $local = Collection::parseIni($locFile);
}

// load yaml config
$config = Collection::make(
    BASE_PATH . '/config/console/config.php',
    'php',
    'console'
)
    ->loadArray(BASE_PATH . "/config/web/{$local['env']}.php")
    ->loadArray($local);

define('RUNTIME_ENV', $config->get('env'));
define('APP_DEBUG', $config->get('debug'));

$app = new \app\cli\App();

// register commands
require BASE_PATH . '/app/cli/routes.php';

$app->run();

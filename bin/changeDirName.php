<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-31
 * Time: 10:54
 * usage: php bin/changeDirName.php dir=app
 */

require dirname(__DIR__) . '/vendor/autoload.php';

use inhere\console\io\Input;
use inhere\console\io\Output;

$in = new Input();
$out = new Output();
$basePath = dirname(__DIR__);

$dir = $in->get('dir');
$path = $basePath . "/$dir";

changeDirName($path);

function changeDirName($path) {
    $list = [];
    echo "change name for the dir path: $path\n";

    foreach (new DirectoryIterator($path) as $item) {
        if ($item->isDir() && !$item->isDot()) {
            $info = [
                'path' => $item->getPath(),
                'name' => $item->getBasename(),
            ];

            $rawPath = $path . '/' . $item->getBasename();
            changeDirName($rawPath);
            $newPath = $path . '/' . ucfirst($item->getBasename());
            rename($rawPath, $newPath);
            $list[] = $info;
        }
    }
}
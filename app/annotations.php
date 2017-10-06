<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/10/6
 * Time: 下午11:51
 */
use Inhere\Library\Files\FileFinder;
use Sws\Annotations\Collector;

$conf = [
//    'base namespace' => 'the real path',
    'App\\' => dirname(__DIR__) . '/app/',
    'Sws\\Components\\' => dirname(__DIR__) . '/lib/sws/Components',
];

$ff = new FileFinder([
    // 'sourcePath' => dirname(__DIR__) . '/app/',
    'include' => [
        'ext' => ['php']
    ],
    'exclude' => [
        'file' => 'Sws.php',
        'dir' => ['Console','Helpers', 'Annotations'], // 排除目录
    ]
]);

$ff->setFileFilter(function ($name) {
    // 必须首字符大写(类文件)
    return preg_match('/[A-Z]/', $name);
});

//$files = $ff->find(true)->getFiles();
//$f2 = $ff->setSourcePath(dirname(__DIR__) . '/lib/sws/Components')->find(true)->getFiles();
//var_dump($files, $f2);

$clt = new Collector($ff, null, $conf);
$clt->addScan('Sws\\Module\\', dirname(__DIR__) . '/lib/sws/Module');

$clt->registerHandlers([
    'route' => new \Sws\Annotations\Handlers\RouteHandler(),
    'service' => new \Sws\Annotations\Handlers\ServiceHandler(),
]);

$clt->handle();

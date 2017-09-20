<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-20
 * Time: 12:45
 */

use Sws\Memory\Language;

require dirname(__DIR__) . '/vendor/autoload.php';

$translator = new Language([
    'basePath' => dirname(__DIR__) . '/resources/langs',
    'langs' => ['en', 'zh-CN']
]);

$translator->scanAndLoad();

$translator['user.test'] = 'new value';

vd(
    $translator['user.test'],
    $translator->tl('user.test'),
    $translator['zh-CN.user.test'],
    $translator['keyNotExist'],
    $translator['user.keyNotExist']
);

//vd($translator->all());

vd($translator->getLangData(null, false));
//vd($translator->getLangData('zh-CN', false));
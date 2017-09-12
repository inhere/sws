<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-12
 * Time: 17:30
 */

namespace Sws\Annotations;

use inhere\library\files\FileFinder;

/**
 * Class Collector
 * @package Sws\Annotations
 */
class Collector
{
    /**
     * @var array
     */
    public $scanDirs = [];

    public function scan()
    {
        new FileFinder([
            'sourcePath' => '/var/xxx/vendor/bower/jquery'
        ]);

        return $this;
    }

    public function getAnnotations()
    {

    }
}
<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-17
 * Time: 9:29
 */

namespace sws\components;

use sws\module\ModuleInterface;

/**
 * Class RouteBag
 * @package sws\components
 */
class RouteBag
{
    /**
     * @var array
     */
    public $data;

    /**
     * @var int
     */
    public $index;

    /**
     * @var ModuleInterface
     */
    public $handler;
}
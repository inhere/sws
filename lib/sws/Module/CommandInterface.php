<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-01
 * Time: 9:49
 */

namespace Sws\Module;

/**
 * Interface CommandInterface
 * @package Sws\Module
 */
interface CommandInterface
{
    public function __invoke();
}
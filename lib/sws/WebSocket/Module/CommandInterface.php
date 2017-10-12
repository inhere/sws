<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-01
 * Time: 9:49
 */

namespace Sws\WebSocket\Module;

/**
 * Interface CommandInterface
 * @package Sws\WebSocket\Module
 */
interface CommandInterface
{
    public function __invoke();
}
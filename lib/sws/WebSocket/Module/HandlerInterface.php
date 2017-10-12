<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-01
 * Time: 9:49
 */

namespace Sws\WebSocket\Module;

/**
 * Interface HandlerInterface
 * @package Sws\WebSocket\Module
 */
interface HandlerInterface
{
    public function run(string $command);
}
<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-25
 * Time: 16:45
 */

namespace Sws\Async;

use Swoole\Async;

/**
 * Class StreamHandler
 * @package Sws\Async
 */
class StreamHandler extends \Monolog\Handler\StreamHandler
{
    public $onWriteEnd;

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        Async::writeFile($this->url, (string) $record['formatted'], $this->onWriteEnd, FILE_APPEND);
    }
}
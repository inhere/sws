<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-28
 * Time: 16:18
 */

namespace Sws\Components;

use Inhere\Server\Components\FileLogHandler;

/**
 * Class AppLogHandler
 * @package Sws\Components
 */
class AppLogHandler extends FileLogHandler
{
    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        if (!$this->server && \Sws::$app) {
            $this->server = \Sws::$app->get('server');
        }

        parent::write($record);
    }
}
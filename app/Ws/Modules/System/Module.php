<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-25
 * Time: 9:26
 */

namespace App\Ws\Modules\System;

use Sws\WebSocket\Module\AbstractModule;

/**
 * Class Module
 * @package App\Ws\Modules
 */
class Module extends AbstractModule
{
    protected function init()
    {
        $this->setCmdHandlers([
            'system/disk/index' => DiskHandler::class,
        ]);
    }
}
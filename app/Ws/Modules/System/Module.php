<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-25
 * Time: 9:26
 */

namespace App\Ws\Modules\System;

use Sws\Module\ModuleAbstracter;

/**
 * Class Module
 * @package App\Ws\Modules
 */
class Module extends ModuleAbstracter
{
    protected function init()
    {
        $this->setCmdHandlers([
            'system/disk/index' => DiskHandler::class,
        ]);
    }
}
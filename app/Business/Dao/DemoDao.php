<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/9/11
 * Time: 下午11:11
 */

namespace App\Dao;

use App\Model\DemoModel;
use Sws\Annotations\Tags\Dao;
use Sws\Annotations\Tags\DI;

/**
 * Class DemoDao
 * @package App\Dao
 *
 * @Dao()
 */
class DemoDao
{
    /**
     * @DI()
     * @var DemoModel
     */
    public $demoModel;
}

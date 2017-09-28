<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-01
 * Time: 9:37
 */

namespace App\Console\Controllers;

use Inhere\Console\Controller;

/**
 * Class DemoController
 * @package App\Console\Controllers
 */
class DemoController extends Controller
{
    protected static $name = 'demo';

    /**
     * the demo command
     */
    public function indexCommand()
    {
        $this->write('hello');
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-28
 * Time: 10:48
 */

namespace App\Console\Controllers;

use Inhere\Console\Controller;

/**
 * Class ServerController
 * @package App\Console\Controllers
 */
class ServerController extends Controller
{
    protected static $name = 'server';

    protected static $description = 'manage the application server start,stop ...';

    /**
     * start the application server
     * @param  \Inhere\Console\IO\Input $input
     * @param  \Inhere\Console\IO\Output $output
     * @return int
     */
    public function startCommand($input, $output)
    {

        return 0;
    }

    private function createApp()
    {
        return ;
    }

    private function createServer()
    {
        return ;
    }
}
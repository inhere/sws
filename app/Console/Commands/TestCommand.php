<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-31
 * Time: 11:49
 */

namespace App\Console\Commands;

use Inhere\Console\Command;
use Inhere\Console\IO\Input;
use Inhere\Console\IO\Output;

/**
 * Class TestCommand
 * @package App\Console\Commands
 */
class TestCommand extends Command
{
    /**
     * do execute
     * @param  Input $input
     * @param  Output $output
     * @return int
     */
    protected function execute($input, $output)
    {
        $this->write('hi');

        return 0;
    }
}
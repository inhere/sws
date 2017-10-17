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
 * Class BuildCommand
 * @package App\Console\Commands
 */
class BuildCommand extends Command
{
    protected static $name = 'build';

    protected static $description = 'build the application phar package.';

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
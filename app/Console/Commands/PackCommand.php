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
use Inhere\Console\Utils\Helper;

/**
 * Class BuildCommand
 * @package App\Console\Commands
 */
class PackCommand extends Command
{
    protected static $name = 'pack';

    protected static $description = 'build the application phar package.';

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        return $this->createDefinition()
            ->addArgument('file', Input::ARG_REQUIRED, 'the dist phar file')
            ->addArgument('base-dir', Input::ARG_REQUIRED, 'the base directory of the package')
            ;
    }

    /**
     * do execute
     * @param  Input $input
     * @param  Output $output
     * @return int
     */
    protected function execute($input, $output)
    {
        $this->write('hi');
        echo Helper::dumpVars($this->input->getArgs());

        return 0;
    }
}

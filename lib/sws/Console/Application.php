<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 17:42
 */

namespace Sws\Console;

use Inhere\Console\Application as BaseApp;
use Sws\ApplicationInterface;
use Sws\ApplicationTrait;

/**
 * Class Application
 * @package App\Console
 */
class Application extends BaseApp implements ApplicationInterface
{
    use ApplicationTrait;

    protected function init()
    {
        \Sws::$app = $this;

        $timeZone = \Sws::get('config')->get('timeZone', 'UTC');

        date_default_timezone_set($timeZone);

        parent::init();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getMeta('name');
    }

}

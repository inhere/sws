<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-24
 * Time: 17:58
 */

namespace App\Ws\Modules;

use Sws\Annotations\Tags\WsModule;

/**
 * Class ChatModule
 * @package App\Ws\Modules
 *
 * @WsModule("chat", path="/chat")
 */
class ChatModule extends \Sws\Module\ChatModule
{
    protected function init()
    {
        parent::init();

        $this->setName('chatRoom');
    }

    public function joinCommand($data)
    {

    }

    public function logoutCommand()
    {

    }

    public function loginCommand()
    {

    }
}

<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-30
 * Time: 16:47
 */

namespace App\Rpc;

/**
 * Class TestService
 * @package App\Rpc
 */
class TestService
{
    public function demoAction()
    {
        return 'hello, this is ' . __METHOD__;
    }
}
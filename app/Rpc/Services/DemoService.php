<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-30
 * Time: 16:47
 */

namespace App\Rpc;

use Sws\Annotations\RpcService;

/**
 * Class DemoService
 *
 * @package App\Rpc
 *
 * @RpcService()
 */
class DemoService
{
    public function index()
    {
        return 'hello, this is ' . __METHOD__;
    }
}

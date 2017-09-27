<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-30
 * Time: 16:47
 */

namespace App\Rpc\Services;

use Sws\Annotations\Tags\RpcService;

/**
 * Class DemoService
 *
 * @package App\Rpc\Services
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

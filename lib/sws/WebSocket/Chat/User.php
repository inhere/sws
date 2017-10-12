<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/9/3
 * Time: 上午1:16
 */

namespace Sws\WebSocket\Chat;

/**
 * Class User
 * @package Sws\WebSocket\Chat
 */
class User
{
    /**
     * @var int
     */
    private $cid;

    /**
     * @return int
     */
    public function getCid()
    {
        return $this->cid;
    }

    /**
     * @param int $cid
     */
    public function setCid($cid)
    {
        $this->cid = $cid;
    }


}

<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-19
 * Time: 14:25
 */

use App\Helper\Respond;

return [
    // basic
    Respond::OK => '操作成功',
    Respond::ERR => '发生错误',
    Respond::FAIL => '操作失败',
    Respond::EXP => '操作异常',

    // category: user(100 ~ 300)
    Respond::NEED_LOGIN => '需要登录访问',
    Respond::NEED_AUTH => '需要认证',

    // category: request(300 ~ 399)
    Respond::MISS_PARAM => '缺少必要参数',
    Respond::PARAM_ERROR => '参数错误',

    // category 1

    // category 2

    // category ...
];
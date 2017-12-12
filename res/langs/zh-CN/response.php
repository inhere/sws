<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-19
 * Time: 14:25
 */

use App\Helper\ResCode;

return [
    // basic
    ResCode::OK => '操作成功',
    ResCode::ERR => '发生错误',
    ResCode::FAIL => '操作失败',
    ResCode::EXP => '操作异常',

    // category: user(100 ~ 300)
    ResCode::NEED_LOGIN => '需要登录访问',
    ResCode::NEED_AUTH => '需要认证',

    // category: request(300 ~ 399)
    ResCode::MISS_PARAM => '缺少必要参数',
    ResCode::PARAM_ERROR => '参数错误',

    // category 1

    // category 2

    // category ...
];
<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-19
 * Time: 14:22
 */

namespace App\Helpers;

/**
 * Class Respond
 * @package App\Helpers
 */
final class Respond
{
    /**
     * @var string
     */
    public static $defaultMsg = 'successful';

    /**
     * @param mixed $data
     * @param int $code
     * @param string $msg
     * @param array $msgArgs
     * @return string
     */
    public static function json($data, int $code = ResCode::OK, string $msg = '', array $msgArgs = [])
    {
        return self::fmtJson($data, $code, $msg, $msgArgs);
    }

    /**
     * @param mixed $data
     * @return string
     */
    public static function rawJson($data)
    {
        return json_encode($data);
    }

    /**
     * @param mixed $data
     * @param int $code
     * @param string $msg
     * @param array $msgArgs
     * @return string
     */
    public static function fmtJson($data, int $code = ResCode::OK, string $msg = '', array $msgArgs = [])
    {
        return json_encode([
            'code' => $code,
            'msg' => $msg ?: self::getMsgByCode($code, $msgArgs),
            'time' => microtime(true),
            'data' => $data,
        ]);
    }

    /**
     * @param $code
     * @param array $msgArgs
     * @return mixed
     */
    public static function getMsgByCode($code, array $msgArgs = [])
    {
        if ($lang = \Sws::app()->getDi()->getIfExist('lang')) {
            $lang->tl('response.' . $code, $msgArgs, self::$defaultMsg);
        }

        return $msg;
    }
}
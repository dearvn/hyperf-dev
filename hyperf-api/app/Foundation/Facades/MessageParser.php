<?php
declare(strict_types = 1);

namespace App\Foundation\Facades;

use Hyperf\Utils\Codec\Json;

/**
 * Message format
 * Class MessageParser
 * @Author YiYuan-Lin
 * @Date: 2021/3/12
 */
class MessageParser
{
    /**
     * @param string $data
     * @return array
     */
    public static function decode(string $data) : array
    {
        return Json::decode($data, true);
    }

    /**
     * @param array $data
     * @return string
     */
    public static function encode(array $data) : string
    {
        return Json::encode($data);
    }

    /**
     * Different message formatting
     * @param \Exception $e
     * @return string
     */
    public static function expMessageParser(\Exception $e) : string
    {
        return 'The error code is：' . $e->getCode() . 'Error message： ' . $e->getMessage() . ':: Error information file location:' . $e->getFile() . ':: Number of error information rows: ' . $e->getLine();
    }
}

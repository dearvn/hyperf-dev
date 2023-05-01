<?php

namespace App\Foundation\Utils;

/**
 * IP positioning tool type
 * Class freeApi
 * @Author YiYuan-Lin
 * @Date: 2021/3/1
 */
class FreeApi{
    /*
     * interface address
     * @var string
     */
    private static $apiUrl = 'https://restapi.amap.com/v3/ip';

    /**
     * key
     * @var string
     */
    private static $appKey = '';

    /**
     * Get IP positioning results
     * @return array
     */

    /**
     * Get IP positioning results
     * @param string $ip
     * @return array
     */
    public static function getResult(string $ip = '') : array
    {
        if (empty($ip)) return [];
        self::$appKey = env('FreeApiKey');
        self::$apiUrl = self::$apiUrl . '?ip=' . $ip . '&key=' . self::$appKey;

        $result = self::freeApiCurl(self::$apiUrl);
        $result = json_decode($result, true);

        if (!empty($result) && $result['status'] == 1) return $result;
        return [];
    }

    /**
     * Request interface criminal content
     * @param $url
     * @param bool $params
     * @param int $isPost
     * @return bool|string
     */
    public static function freeApiCurl($url, $params=false, $isPost=0)
    {
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
        curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
        curl_setopt( $ch, CURLOPT_USERAGENT , 'free-api' );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 60 );
        curl_setopt( $ch, CURLOPT_TIMEOUT , 60);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
        if( $isPost )
        {
            curl_setopt( $ch , CURLOPT_POST , true );
            curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
            curl_setopt( $ch , CURLOPT_URL , $url );
        }
        else
        {
            if($params){
                curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
            }else{
                curl_setopt( $ch , CURLOPT_URL , $url);
            }
        }
        $response = curl_exec( $ch );
        if ($response === FALSE) {
            return false;
        }
        curl_close( $ch );
        return $response;
    }
}
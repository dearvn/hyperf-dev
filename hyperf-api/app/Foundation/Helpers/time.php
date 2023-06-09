<?php
/**
 * Processing time related functions
 * create by linyiyuan
 */

if (!function_exists('timeRoundFive')) {
    /**
     * Take the time according to five minutes
     * 11:02:23 => 11:05:00
     * 11:06:34 => 11:10:00
     *
     * @param string $time
     * @return array|mixed
     */
    function timeRoundFive($time){
        $hour = date('i', $time);
        if ($hour == 0) {
            $h = date('H');
            return strtotime(date('Y-m-d', $time) . $h  . ':' . '00' . ':' . '00');
        }
        if ($hour > 55) {
            $h = date('H') + 1;
            return strtotime(date('Y-m-d', $time) . $h  . ':' . '00' . ':' . '00');
        }
        $last_one = intval(substr($hour,-1,1));
        $last_two = intval(substr($hour,-2,1));
        if($last_one > 5){
            $last_one = 0;
            $last_two = $last_two+1;
        }else{
            $last_one = 5;
        }
        $hour = substr_replace($hour,$last_one,-1,1);
        $hour = substr_replace($hour,$last_two,-2,1);

        return strtotime(date('Y-m-d H', $time) . ':' . $hour . ':' . '00');
    }
}

if (!function_exists('timeRoundBeforeFive')) {
    /**
     * Take the time according to five minutes
     * 11:02:23 => 11:00:00
     * 11:06:34 => 11:05:00
     *
     * @param string $time
     * @return array|mixed
     */
    function timeRoundBeforeFive($time){
        $hour = date('i', $time);
        if($hour == 0) {
            return strtotime(date('Y-m-d', $time) . (date('H', $time) - 1) . ':' . 55 . ':' . '00');
        }
        $last_one = intval(substr($hour,-1,1));
        if($last_one > 5){
            $last_one = 5;
        }else{
            $last_one = 0;
        }
        $hour = substr_replace($hour,$last_one,-1,1);

        return strtotime(date('Y-m-d H', $time) . ':' . $hour . ':' . '00');
    }
}

if (!function_exists('periodConvert')) {
    /**
     * Convert the time period to 48 -bit characters 1 0
     * @param $period
     * @return string
     */
    function periodConvert($period) {
        $result = '';
        foreach ($period as $k1 => $v1) {
            $temp = '000000000000000000000000000000000000000000000000';
            if ($k1 > 6) continue;
            if (empty($v1)) {
                $result .= $temp;
                continue;
            }
            foreach ($v1 as $k2 => $v2) {
                for ($i = intval(($v2[0] * 2)); $i < intval($v2[1] * 2); $i ++ ) {
                    $temp[$i] = 1;
                }
            }

            $result .= $temp;
        }
        return $result;
    }
}

if (!function_exists('getMillisecond')) {
    /**
     * Get millisecond time stamp
     * @return float
     */
    function getMillisecond()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float) sprintf('%.0f', (floatval($t1)+floatval($t2)) * 1000);
    }
}


if (!function_exists('getRangeBetweenTime')) {
    /**
     * Scope
     * @param $begin
     * @param $end
     * @return float|int
     */
    function getRangeBetweenTime($begin, $end) {
        $beginDate = strtotime(date('Y-m-d', $begin));
        $endDate = strtotime(date('Y-m-d', $end));

        return ($endDate - $beginDate) / 86400;
    }
}

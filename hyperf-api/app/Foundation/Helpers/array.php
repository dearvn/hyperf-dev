<?php

/**
 * Public array function
 * create by linyiyuan
 */

if (!function_exists('cleanArrayNull')) {
    /**
     * Remove the array containing the NULL value
     * @param $arr
     * @return array
     */
    function cleanArrayNull($arr)
    {
      if (empty($arr)) return [];

      foreach ($arr as $key => $val) {
          if (is_null($val)) unset($arr[$key]);
      }

      return $arr;
    }
}


if (!function_exists('changeKeysToNum')) {
    /**
     * Convert the key value of the array to digital plastic surgery
     * @param array $arr Array to be converted
     * @return array
     */
    function changeKeysToNum($arr, $except = []){
        if (!is_array($arr)) return $arr;

        $i = 0;
        $new = array();
        foreach ($arr as $key => $value) {
            if(is_array($value) && !in_array($key, $except)){
                $new[$i] = changeKeysToNum($value, $except);
            } else {
                $new[$key] = $value;
            }
            $i++;
        }

        return $new;
    }
}

if (!function_exists('getArraysByLimit')) {
    /**
     * Send a certain part of the array
     * @param $arr
     * @param $cur
     * @param $size
     * @return mixed
     */
    function getArraysByLimit($arr, $cur, $size) {
        if (!is_array($arr)) return $arr;

        $cur_page = $cur ?? 1;
        $page_size = $size ?? 20;

        $offset = ($cur_page- 1) * $page_size;
        $limit  = $page_size;
        $newArr = array_slice($arr, $offset, $limit);

        return $newArr;
    }
}


if (!function_exists('arrayToStringArray')) {
    /**
     * Convert the array into a string formal accommodation
     * @param $arr
     * @return mixed
     */
    function arrayToStringArray($arr) {
        if (!is_array($arr) || empty($arr)) return $arr;
        $str = '[';
        foreach ($arr as $key => $val) {
            if (is_string($val)) {
                $str .= '"' . $val . '",';
            }else {
                $str .= $val . ',';
            }
        }
        $str = substr($str, 0, -1);
        $str .= ']';

        return $str;
    }
}

if (!function_exists('arrayToTree')) {
    /**
    * The one-dimensional array of non-hierarchical fields is converted into an array of attribute structures according to the parent ID
    *@param array $data [data to be converted]
    *@param string $pkName [Primary key ID field name]
    *@param string $pIdName [parent ID field name]
    *@param string $childName [Field to save child data]
    *@param bool $emptyChildren [Whether to display empty child fields when there is no child data]
    *@param string $rootId [root ID]
    *@return array
    */
    function arrayToTree($data, $pkName='id', $pIdName='parent_id', $childName='children', $emptyChildren=false, $rootId='') {
        $returnData = [];
        foreach($data as $v){
            if($v[$pIdName] == $rootId){
                $res = arrayToTree($data, $pkName, $pIdName, $childName, $emptyChildren, $v[$pkName]);
                if(!empty($res)){
                    $v[$childName] = $res;
                } else {
                    if ($emptyChildren) {
                        $v[$childName] = [];
                    }
                }
                $returnData[] = $v;
            }
        }
        return $returnData;
    }
}
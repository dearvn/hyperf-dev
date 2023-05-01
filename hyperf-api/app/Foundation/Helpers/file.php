<?php
/**
 * 文件相关函数
 */
if (!function_exists('base64DecImg')) {
    /**
    * Decompile data/base64 data stream and create image file
    *@author Yiyuan-Lin
    *@param string $baseData data/base64 data stream
    *@param string $dir Store image file directory
    *@param string $fileName image file name (without file suffix)
    *@return mixed return newly created file path or boolean type
    */
    function base64DecImg($baseData, $dir = '', $fileName = ''){
        $expData = explode(';', $baseData);
        $postfix   = explode('/', $expData[0]);
        if(strstr($postfix[0], 'image') ){
            if(!is_readable($dir)) mkdir($dir, 0700);
            $postfix   = $postfix[1] == 'jpeg' ? 'jpg' : $postfix[1];
            $storageDir = $dir . $fileName . '.' . $postfix;
            $export = base64_decode(str_replace("{$expData[0]};base64,", '', $baseData));
            file_put_contents($storageDir, $export);
            return [
                'storage_dir' => $storageDir,
                'ext' => $postfix,
            ];
        }
    }
}


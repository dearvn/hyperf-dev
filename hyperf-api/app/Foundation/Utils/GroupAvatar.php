<?php

namespace App\Foundation\Utils;

use App\Constants\StatusCode;
use App\Exception\Handler\BusinessException;
use App\Service\Common\UploadService;

/**
 * Talk about the avatar according to the avatar synthesis group
 * Class GroupAvatar
 * @package App\Foundation\Utils
 * @Author YiYuan-Lin
 * @Date: 2021/5/22
 */
class GroupAvatar
{
    /**
     * Picture list
     * @var array
     */
    private static $picList = [];

    /**
     * Whether to keep
     * @var bool
     */
    private static $isSave = true;

    /**
     * save route
     * @var string
     */
    private static $savePath = '';

    /**
     * Canvas width
     * @var int
     */
    public static $width = 400;

    /**
     * Height
     * @var int
     */
    public static $height = 400;

    /**
     * Initialize some parameters
     * @param array $picList
     * @param bool $isSave
     * @param string $savePath
     */
    public static function init(array $picList, bool $isSave = true, string $savePath = 'chat/group/avatar')
    {
        self::$picList = $picList;
        self::$isSave = $isSave;
        self::$savePath = $savePath;
    }

    /**
     * Generate pictures
     * @return bool|string
     * @throws \League\Flysystem\FileExistsException
     */
    public static function build() : string
    {
        //Verification parameter
        if(empty(self::$picList)) throw new BusinessException(StatusCode::ERR_VALIDATION, '图片列表数据不能为空');
        //If you need to save, you need to pass the preservation address
        if(self::$isSave && empty(self::$savePath)) throw new BusinessException(StatusCode::ERR_VALIDATION, '图片保存路径不能为空');

        $res = self::generateCanvas();
        if (!$res) throw new BusinessException(StatusCode::ERR_EXCEPTION, '合成图片失败');
        return $res;
    }

    /**
     * Generate canvas
     * @return bool|string
     * @throws \League\Flysystem\FileExistsException
     */
    private static function generateCanvas()
    {
        // Only operate the first 9 pictures
        $picList = array_slice(self::$picList, 0, 9);
        //Create a new color image as the background
        $background = imagecreatetruecolor(self::$width, self::$height);
        //Create a white -gray background for the true color canvas, and then set it to transparent
        $color = imagecolorallocate($background, 202, 201, 201);
        imagefill($background, 0, 0, $color);
        imageColorTransparent($background, $color);
        //Set the picture location according to the number of pictures
        $picCount = count($picList);
        $lineArr = array();//Need to change the location
        $space_x = 3;
        $space_y = 3;
        $line_x = 0;
        switch($picCount) {
            case 1:
                //The middle
                $start_x = intval(self::$width / 4); // Starting position x
                $start_y = intval(self::$height / 4); // Starting position Y
                $pic_w = intval(self::$width / 2); // width
                $pic_h = intval(self::$height / 2); // high
                break;
            case 2:
                //Midtop is parallel
                $start_x = 2;
                $start_y = intval(self::$height / 4) + 3;
                $pic_w = intval(self::$width / 2) - 5;
                $pic_h = intval(self::$height / 2) - 5;
                $space_x = 5;
                break;
            case 3:
                $start_x = 40; // Starting position x
                $start_y = 5; // Starting position Y
                $pic_w = intval(self::$width / 2) - 5; // width
                $pic_h = intval(self::$height / 2) - 5; // high
                $lineArr = array(2);
                $line_x = 4;
                break;
            case 4:
                $start_x = 4; // Starting position x
                $start_y = 5; // Starting position Y
                $pic_w = intval(self::$width / 2) - 5; // width
                $pic_h = intval(self::$height / 2) - 5; // high
                $lineArr = array(3);
                $line_x = 4;
                break;
            case 5:
                $start_x = 30; // Starting position x
                $start_y = 30; // Starting position Y
                $pic_w = intval(self::$width / 3) - 5; // width
                $pic_h = intval(self::$height / 3) - 5; // high
                $lineArr = array(3);
                $line_x = 5;
                break;
            case 6:
                $start_x = 5; // Starting position x
                $start_y = 30; // Starting position y
                $pic_w = intval(self::$width / 3) - 5; // width
                $pic_h = intval(self::$height / 3) - 5; // high
                $lineArr = array(4);
                $line_x = 5;
                break;
            case 7:
                $start_x = 53; // Starting position x
                $start_y = 5; // Starting position y
                $pic_w = intval(self::$width / 3) - 5; // width
                $pic_h = intval(self::$height / 3) - 5; // high
                $lineArr = array(2,5);
                $line_x = 5;
                break;
            case 8:
                $start_x = 30; // Starting position x
                $start_y = 5; // Starting position y
                $pic_w = intval(self::$width / 3) - 5; // width
                $pic_h = intval(self::$height / 3) - 5; // high
                $lineArr = array(3,6);
                $line_x = 5;
                break;
            case 9:
                $start_x = 5; // Starting position x
                $start_y = 5; // Starting position y
                $pic_w = intval(self::$width / 3) - 5; // width
                $pic_h = intval(self::$height / 3) - 5; // high
                $lineArr = array(4,7);
                $line_x = 5;
                break;
        }
        foreach($picList as $k => $pic_path ) {
            $kk = $k + 1;
            if ( in_array($kk, $lineArr) ) {
                $start_x = $line_x;
                $start_y = $start_y + $pic_h + $space_y;
            }
            //Get the image file extension type and mime type to determine whether it is a normal image file
            //Abnormal image files, the corresponding position is empty, skip processing
            $image_mime_info = @getimagesize($pic_path);
            if($image_mime_info && !empty($image_mime_info['mime'])){
                $mime_arr = explode('/',$image_mime_info['mime']);
                if(is_array($mime_arr) && $mime_arr[0] == 'image' && !empty($mime_arr[1])){
                    switch($mime_arr[1]) {
                        case 'jpg':
                        case 'jpeg':
                            $imageCreateFromJpeg = 'imagecreatefromjpeg';
                            break;
                        case 'png':
                            $imageCreateFromJpeg = 'imagecreatefrompng';
                            break;
                        case 'gif':
                        default:
                            $imageCreateFromJpeg = 'imagecreatefromstring';
                            $pic_path = file_get_contents($pic_path);
                            break;
                    }
                    //Create a new image
                    $resource = $imageCreateFromJpeg($pic_path);
                    //Copy a rectangular area of ​​an image to another background image
                    //$start_x, $start_y the starting position to place in the background
                    //0,0 The starting position of the cropped source avatar
                    //High and width after $pic_w, $pic_h copy
                    imagecopyresized($background, $resource, $start_x, $start_y, 0, 0, $pic_w, $pic_h, imagesx($resource), imagesy($resource));
                }
            }
            // The last two parameters are the original picture width and high, and the countdown of the two parameters is COPY pictures Width and High
            $start_x = $start_x + $pic_w + $space_x;
        }
        ob_start();
        imagejpeg($background, null, 100);
        $imageData = ob_get_contents();
        ob_end_clean();
        imagedestroy($background);

        $fileUrl = UploadService::getInstance()->uploadPicByContent($imageData, self::$savePath);

        return $fileUrl;
    }
}
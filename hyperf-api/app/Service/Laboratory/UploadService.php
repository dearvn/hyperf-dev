<?php
namespace App\Service\Laboratory;

use App\Constants\UploadCode;
use App\Foundation\Traits\Singleton;
use App\Service\BaseService;
use Hyperf\Di\Annotation\Inject;
use League\Flysystem\Filesystem;
use function mysql_xdevapi\getSession;

/**
 * Chat module upload service category
 * Class UploadService
 * @package App\Service\Laboratory
 * @Author YiYuan-Lin
 * @Date: 2021/3/18
 */
class UploadService extends BaseService
{
    use Singleton;

    /**
     * @Inject()
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Upload the picture according to the base64 format
     * @param string $file
     * @param string $savePath
     * @return array
     * @throws \League\Flysystem\FileExistsException
     */
    public function uploadSinglePicByBase64(string $file, string $savePath = '') : array
    {
        //Stringing the file name and the corresponding path
        $fileName =  md5(uniqid());
        $dir = './runtime/temp/';
        $imageInfo = base64DecImg($file, $dir, $fileName);
        $uploadPath = $savePath . '/' . $fileName . '.' .$imageInfo['ext'];

        //The path of external network visits
        $fileUrl = env('OSS_URL') . $uploadPath;
        $stream = fopen($imageInfo['storage_dir'], 'r+');
        $this->filesystem->writeStream(
            $uploadPath,
            $stream
        );
        if (is_resource($stream)) {
            fclose($stream);
        }
        unlink($imageInfo['storage_dir']);
        return [
            'fileName' => $fileName,
            'url' => $fileUrl
        ];
    }

    /**
     * 上传图片
     * @param object $file
     * @param string $savePath
     * @param string $messageId
     * @return array
     * @throws \League\Flysystem\FileExistsException
     */
    public function uploadPic(object $file, string $savePath = '', string $messageId = '') : array
    {
        if ($file->getSize() > 5242880) $this->throwExp(UploadCode::ERR_UPLOAD_SIZE, '上传图片不能超过5M');
        //Get the suffix of the upload file
        $fileExt = getExtByFile($file->getClientFilename());

        //Stringing the file name and the corresponding path
        $fileName =  md5(uniqid())  . '.' . $fileExt;
        $uploadPath = $savePath . '/' . $fileName;

        //The path of external network visits
        $fileUrl = env('OSS_URL') . $uploadPath;

        $stream = fopen($file->getRealPath(), 'r+');
        $this->filesystem->writeStream(
            $uploadPath,
            $stream
        );
        if (is_resource($stream)) {
            fclose($stream);
        }
        return [
            'fileName' => $fileName,
            'url' => $fileUrl,
            'messageId' => $messageId,
        ];
    }

    /**
     * upload files
     * @param object $file
     * @param string $savePath
     * @param string $messageId
     * @return array
     * @throws \League\Flysystem\FileExistsException
     */
    public function uploadFile(object $file, string $savePath = '', string $messageId = '') : array
    {
        if ($file->getSize() > 20971520) $this->throwExp(UploadCode::ERR_UPLOAD_SIZE, '上传文件不能超过20M');
        //Get the suffix of the upload file
        $fileExt = getExtByFile($file->getClientFilename());

        //Stringing the file name and the corresponding path
        $fileName =  md5(uniqid())  . '.' . $fileExt;
        $uploadPath = $savePath . '/' . $fileName;

        //The path of external network visits
        $fileUrl = env('OSS_URL') . $uploadPath;

        $stream = fopen($file->getRealPath(), 'r+');
        $this->filesystem->writeStream(
            $uploadPath,
            $stream
        );
        if (is_resource($stream)) {
            fclose($stream);
        }
        return [
            'fileName' => $fileName,
            'fileExt' => $fileExt,
            'url' => $fileUrl,
            'messageId' => $messageId,
        ];
    }

    /**
     * Upload video file
     * @param object $file
     * @param string $savePath
     * @param string $messageId
     * @return array
     * @throws \League\Flysystem\FileExistsException
     */
    public function uploadVideo(object $file, string $savePath = '', string $messageId = '') : array
    {
        if ($file->getSize() > 20971520) $this->throwExp(UploadCode::ERR_UPLOAD_SIZE, '上传文件不能超过20M');
        //Get the suffix of the upload file
        $fileExt = getExtByFile($file->getClientFilename());
        if (!in_array($fileExt, ['mp4', 'avi', 'mov', 'rmvb', 'flv', '3GP', 'FLV', 'WMV'])) $this->throwExp(UploadCode::ERR_UPLOAD_TYPE, '上传文件必须为视频文件');

        //Stringing the file name and the corresponding path
        $fileName =  md5(uniqid())  . '.' . $fileExt;
        $uploadPath = $savePath . '/' . $fileName;

        //The path of external network visits
        $fileUrl = env('OSS_URL') . $uploadPath;

        $stream = fopen($file->getRealPath(), 'r+');
        $this->filesystem->writeStream(
            $uploadPath,
            $stream
        );
        if (is_resource($stream)) {
            fclose($stream);
        }
        return [
            'fileName' => $fileName,
            'fileExt' => $fileExt,
            'url' => $fileUrl,
            'messageId' => $messageId,
        ];
    }
}

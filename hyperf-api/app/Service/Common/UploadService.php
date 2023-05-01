<?php
namespace App\Service\Common;

use App\Constants\UploadCode;
use App\Foundation\Traits\Singleton;
use App\Service\BaseService;
use Hyperf\Di\Annotation\Inject;
use League\Flysystem\Filesystem;
use phpDocumentor\Reflection\Types\Resource_;

class UploadService extends BaseService
{
    use Singleton;

    /**
     * @Inject()
     * @var Filesystem
     */
    private $filesystem;

    /**
     * upload image
     * @param object $file
     * @param string $savePath
     * @return array
     * @throws \League\Flysystem\FileExistsException
     */
    public function uploadSinglePic(object $file, string $savePath = '') : array
    {
        if ($file->getSize() > 30000000) $this->throwExp(UploadCode::ERR_UPLOAD_SIZE, '上传图片尺寸过大');
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
            'url' => $fileUrl
        ];
    }

    /**
     * upload image
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
     * Upload the picture according to the Blob file type
     * @param object $file
     * @param string $savePath
     * @return array
     * @throws \League\Flysystem\FileExistsException
     */
    public function uploadSinglePicByBlob(object $file, string $savePath = '') : array
    {
        if ($file->getSize() > 30000000) $this->throwExp(UploadCode::ERR_UPLOAD_SIZE, '上传图片尺寸过大');

        //Stringing the file name and the corresponding path
        $fileName =  md5(uniqid())  . '.' . 'jpg';
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
            'url' => $fileUrl
        ];
    }

    /**
     * Upload pictures according to the content of the file
     * @param $content
     * @param string $savePath
     * @return string | bool
     * @throws \League\Flysystem\FileExistsException
     */
    public function uploadPicByContent(string $content, string $savePath = '')
    {
        if (empty($content)) return false;
        //Stringing the file name and the corresponding path
        $fileName =  md5(uniqid())  . '.' . 'jpg';
        $uploadPath = $savePath . '/' . $fileName;

        //The path of external network visits
        $fileUrl = env('OSS_URL') . $uploadPath;
        $this->filesystem->write(
            $uploadPath,
            $content
        );

        return $fileUrl;
    }
}

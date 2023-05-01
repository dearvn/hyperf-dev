<?php

declare(strict_types=1);

namespace App\Controller\Common;

use App\Middleware\RequestMiddleware;
use App\Controller\AbstractController;
use App\Service\Common\UploadService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;

/**
 * Public upload interface controller
 * @Controller(prefix="common/upload")
 */
class UploadController extends AbstractController
{
    /**
     * Upload single picture interface
     * @RequestMapping(path="single_pic", methods="post")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     * })
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \League\Flysystem\FileExistsException
     */
    public function uploadSinglePic()
    {
        $params = [
            'savePath' => $this->request->input('savePath'),
            'file' => $this->request->file('file'),
        ];
        //Configuration verification
        $rules = [
            'savePath' => 'required',
            'file' => 'required |file|image',
        ];
        $message = [
            'savePath.required' => '[savePath] required',
            'file.required' => '[file]Lack',
            'file.file' => '[file] The parameter must be the file type',
            'file.image' => '[file] The file must be a picture（jpeg、png、bmp、gif 或者 svg）',
        ];
        $this->verifyParams($params, $rules, $message);

        $uploadResult = UploadService::getInstance()->uploadSinglePic($this->request->file('file'), $params['savePath']);

        return $this->success($uploadResult, 'Upload the picture successfully');
    }

    /**
     * Upload single picture interface
     * @RequestMapping(path="single_pic_by_base64", methods="post")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     * })
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \League\Flysystem\FileExistsException
     */
    public function uploadSinglePicByBase64()
    {
        $params = [
            'savePath' => $this->request->input('savePath'),
            'file' => $this->request->input('file'),
        ];
        //Configuration verification
        $rules = [
            'savePath' => 'required',
            'file' => 'required ',
        ];
        $message = [
            'savePath.required' => '[savePath] required',
            'file.required' => '[file] required',
        ];
        $this->verifyParams($params, $rules, $message);

        base64DecImg($params['file']);
        $uploadResult = UploadService::getInstance()->uploadSinglePicByBase64($params['file'], $params['savePath']);
        return $this->success($uploadResult);
    }

    /**
     * Upload single picture according to the BLOB file type
     * @RequestMapping(path="single_pic_by_blob", methods="post")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     * })
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \League\Flysystem\FileExistsException
     */
    public function uploadSinglePicByBlob()
    {
        $params = [
            'savePath' => $this->request->input('save_path'),
            'file' => $this->request->file('file'),
        ];
        //Configuration verification
        $rules = [
            'savePath' => 'required',
            'file' => 'required|file',
        ];
        $message = [
            'savePath.required' => '[savePath] required',
            'file.required' => '[file] required',
            'file.file' => '[file] The parameter must be the file type',
        ];
        $this->verifyParams($params, $rules, $message);

        $uploadResult = UploadService::getInstance()->uploadSinglePicByBlob($params['file'], $params['savePath']);
        return $this->success($uploadResult);
    }
}
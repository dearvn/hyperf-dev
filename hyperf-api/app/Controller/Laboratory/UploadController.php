<?php

declare(strict_types=1);

namespace App\Controller\Laboratory;

use App\Middleware\RequestMiddleware;
use App\Controller\AbstractController;
use App\Service\Laboratory\UploadService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;

/**
 * Chat module upload interface controller
 * @Controller(prefix="/laboratory/chat_module")
 */
class UploadController extends AbstractController
{
    /**
     * Upload single picture interface
     * @RequestMapping(path="upload_pic_by_base64", methods="post")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     * })
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \League\Flysystem\FileExistsException
     */
    public function uploadPicByBase64()
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
     * upload image
     * @RequestMapping(path="upload_pic", methods="post")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     * })
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \League\Flysystem\FileExistsException
     */
    public function uploadPic()
    {
        $params = [
            'savePath' => $this->request->input('savePath') ?? '',
            'file' => $this->request->file('file'),
            'messageId' => $this->request->input('messageId') ?? ''
        ];
        //Configuration verification
        $rules = [
            'savePath' => 'required',
            'file' => 'required|file|image',
            'messageId' => 'required ',
        ];
        $message = [
            'savePath.required' => '[savePath] required',
            'file.required' => '[file] required',
            'file.file' => '[file] the parameter must be the file type',
            'file.image' => '[file] the file must be pictures (JPEG, PNG, BMP, GIF or SVG)',
            'messageId.required' => '[messageId] required',
        ];
        $this->verifyParams($params, $rules, $message);

        $uploadResult = UploadService::getInstance()->uploadPic($params['file'], $params['savePath'], $params['messageId']);
        return $this->success($uploadResult);
    }

    /**
     * upload files
     * @RequestMapping(path="upload_file", methods="post")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     * })
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \League\Flysystem\FileExistsException
     */
    public function uploadFile()
    {
        $params = [
            'savePath' => $this->request->input('savePath') ?? '',
            'file' => $this->request->file('file'),
            'messageId' => $this->request->input('messageId') ?? ''
        ];
        //Configuration verification
        $rules = [
            'savePath' => 'required',
            'file' => 'required|file',
            'messageId' => 'required ',
        ];
        $message = [
            'savePath.required' => '[savePath] required',
            'file.required' => '[file] required',
            'file.file' => '[file] 参数必须为文件类型',
            'messageId.required' => '[messageId] required',
        ];
        $this->verifyParams($params, $rules, $message);

        $uploadResult = UploadService::getInstance()->uploadFile($params['file'], $params['savePath'], $params['messageId']);
        return $this->success($uploadResult);
    }

    /**
     * upload files
     * @RequestMapping(path="upload_video", methods="post")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     * })
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \League\Flysystem\FileExistsException
     */
    public function uploadVideo()
    {
        $params = [
            'savePath' => $this->request->input('savePath') ?? '',
            'file' => $this->request->file('file'),
            'messageId' => $this->request->input('messageId') ?? ''
        ];
        //Configuration verification
        $rules = [
            'savePath' => 'required',
            'file' => 'required|file',
            'messageId' => 'required ',
        ];
        $message = [
            'savePath.required' => '[savePath] required',
            'file.required' => '[file] required',
            'messageId.required' => '[messageId] required',
            'file.file' => '[file] The parameter must be the file type',
        ];
        $this->verifyParams($params, $rules, $message);

        $uploadResult = UploadService::getInstance()->uploadVideo($params['file'], $params['savePath'], $params['messageId']);
        return $this->success($uploadResult);
    }
}
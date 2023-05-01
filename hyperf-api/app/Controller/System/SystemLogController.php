<?php

namespace App\Controller\System;

use App\Constants\StatusCode;
use App\Controller\AbstractController;
use App\Foundation\Annotation\Explanation;
use App\Service\System\SystemLogService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Middleware\RequestMiddleware;
use App\Middleware\PermissionMiddleware;


/**
 * Class SystemLogController
 * @Controller(prefix="setting/log_module/system_log")
 * @package App\Controller\System
 * @Author YiYuan-Lin
 * @Date: 2021/03/04
 */
class SystemLogController extends AbstractController
{
    /**
     * Log directory
     * @var
     */
    protected $log_path;

    /**
     * The error log is regular matching expression
     */
    const LOG_ERROR_PATTER = '/^(?<datetime>.*)\|\|(?<env>\w+)\|\|(?<level>\w+)\|\|(.*?)\:(?<message>.*)/m';

    /**
     * SQL query regular matching expression
     */
    const LOG_SQL_PATTER = '';

    /**
     * Error log
     * @RequestMapping(path="error_log", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function errorLog()
    {
        $logPath = config('log_path') . 'hyperf_error';
        $fileList = SystemLogService::getInstance()->scanDirectory($logPath);
        if (!$fileList) $this->throwExp(StatusCode::ERR_EXCEPTION, 'There is no log record file for this project');

        $list = [];
        $total = 0;
        // Get the file tree shape list
        $files = $fileList['files'];
        $total = count($files);
        // The circular directory finds the file in this directory
        foreach ($files as $key => $value) {

        }

    }





    /**
     * Get the system log list
     * @RequestMapping(path="log_path", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getLogPath()
    {


        $fileList = SystemLogService::getInstance()->scanDirectory($this->log_path);
        if (!$fileList) $this->throwExp(StatusCode::ERR_EXCEPTION, 'There is no log record file for this project');

        // Get the file tree shape list
        $fileTree = [];
        $dirs = $fileList['dirs'];
        $files = $fileList['files'];
        // The circular directory finds the file in this directory
        foreach ($dirs as $key => $value) {
            $fileTree[$key]['type'] = "dir";
            $fileTree[$key]['path'] = $value;
            $pattern = '/' . str_replace(["\\", "/"], "", $value) . '/';
            foreach ($files as $k => $v) {
                $v = str_replace(["\\", "/"], "", $v);

                if (preg_match($pattern, $v, $temp)) {
                    if (!isset($fileTree[$key]['children'])) $fileTree[$key]['children'] = [];
                    array_unshift($fileTree[$key]['children'], [
                        'type' => 'file',
                        'path' => $files[$k],
                        'dir' => substr($value, strripos($value, "/") + 1)
                    ]);
                    unset($files[$k]);
                };
            }
        }
        // If there is a file that is not matched, it is a file in the first -level directory
        if (!empty($files)) {
            $files = array_reverse($files);
            foreach ($files as $k => $v) {
                array_push($fileTree, [
                    'type' => 'file',
                    'path' => $v
                ]);
            }
        }

        return $this->success([
            'list' => $fileTree,
            'total' => count($fileTree),
        ]);
    }

    /**
     * Get the content of the log file
     * @RequestMapping(path="log_content", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getLogContent()
    {
        $path = $this->request->input('file_path') ?? '';
        if (empty($path)) $this->throwExp(StatusCode::ERR_EXCEPTION, 'Please select the log file');

        // Get the content in order
        $content = SystemLogService::getInstance()->getLogContent($path, self::LOG_PATTER);
        if (!empty($content)) $content=array_reverse($content);
        $total = count($content);

        // Pagination
        $curPage = $this->params['cur_page'] ?? 1;
        $pageSize = $this->params['page_size'] ?? 20;
        $contents = array_chunk($content, $pageSize);
        $content = $contents[$curPage - 1];

        return $this->success([
            'list' => $content,
            'total' => $total
        ]);
    }

    /**
     * @Explanation(content="Delete log")
     * @RequestMapping(path="destroy_log", methods="delete")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteLog()
    {
        $path = $this->request->input('path') ?? '';
        $path = urldecode($path);

        if (!file_exists($path)) $this->throwExp(StatusCode::ERR_EXCEPTION, 'file does not exist');
        if (!unlink($path)) $this->throwExp(StatusCode::ERR_EXCEPTION, 'failed to delete');

        return $this->successByMessage('Delete files successfully');
    }
}
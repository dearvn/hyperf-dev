<?php

declare(strict_types=1);

namespace App\Controller\Common;

use App\Model\System\Notice;
use App\Model\System\OperateLog;
use App\Middleware\RequestMiddleware;
use App\Controller\AbstractController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;

/**
 * Dashboard data
 * @Controller
 */
class HomeController extends AbstractController
{
    /**
     * Get homepage data
     * @RequestMapping(path="/home", methods="get,post")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     * })
     */
    public function home()
    {
        $noticeList = $this->getNoticeInfo();
        $operateLogList = $this->getOperateLog();

        return $this->success([
            'notice_list' => $noticeList,
            'operate_log' => $operateLogList
        ], 'Get homepage data successfully');
    }

    /**
     * Get the notification information
     * @return array
     */
    private function getNoticeInfo() : array
    {
        $noticeQuery = Notice::query();
        $noticeQuery->where('public_time', '<=', time());
        $noticeQuery->where('status', Notice::ON_STATUS);
        $noticeQuery->orderBy('public_time', 'desc');

        $noticeQuery->select('title', 'content', 'public_time');
        $list = $noticeQuery->get()->toArray();

        return $list;
    }

    /**
     * Obtain the operation log list
     * @return array
     */
    private function getOperateLog() : array
    {
        $operateLog = OperateLog::query();
        $operateLog->orderBy('created_at', 'desc');
        $operateLog->where('uid', conGet('user_info')['id']);
        $total = $operateLog->count();
        $operateLog = $this->pagingCondition($operateLog, $this->request->all());

        $list = $operateLog->get()->toArray();

        return [
            'list' => $list,
            'total' => $total
        ];
    }

    /**
     * Get map data
     * @RequestMapping(path="/world_map_data", methods="get,post")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     * })
     */
    public function getWorldMapData()
    {
        return $this->success($this->_getWorldMapData(), 'Get the map data successfully');
    }

    /**
     * Get map data
     * @return array
     */
    private function _getWorldMapData() : array
    {
        return config('worldMap.data');
    }
}
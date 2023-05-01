<?php
declare(strict_types=1);

namespace App\Controller\Laboratory\Bilibili;

use App\Constants\StatusCode;
use App\Controller\AbstractController;
use App\Foundation\Annotation\Explanation;
use App\Foundation\Utils\Queue;
use App\Job\Bilibili\SyncVideoFromUpUserJob;
use App\Job\Bilibili\UpUserInfoRecordJob;
use App\Model\Laboratory\Bilibili\UpUser;
use App\Model\Laboratory\Bilibili\UpUserReport;
use App\Service\Laboratory\Bilibili\UpUserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Middleware\RequestMiddleware;
use App\Middleware\PermissionMiddleware;

/**
 * Up lord
 * Class UpUserController
 * @Controller(prefix="laboratory/bilibili_module/up_user")
 */
class UpUserController extends AbstractController
{
    /**
     * @Inject()
     * @var UpUser
     */
    private $upUser;

    /**
     * @Inject()
     * @var UpUserReport
     */
    private $upUserReport;

    /**
     * @Inject()
     * @var Queue
     */
    private $queue;

    /**
     * @Explanation(content="Enter the UP Lord")
     * @RequestMapping(path="up_user_add", methods="post")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     * @throws \Exception
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function upUserAdd()
    {
        $upUserInfo = $this->request->all()['up_user_info'] ?? [];
        if (empty($upUserInfo)) $this->throwExp(StatusCode::ERR_VALIDATION, 'UP main information cannot be empty');

        //Is there an emptuserurl?
        $isExistEmptyUrl = false;
        $addMidArr = [];
        foreach ($upUserInfo as $upUser) {
            $upUserUrl = $upUser['up_user_url'] ?? '';
            $timedStatus = $upUser['timed_status'] ?? '';
            if (empty($upUserUrl)) {
                $isExistEmptyUrl = true;
                continue;
            }
            $lastString = basename($upUserUrl);
            $mid = explode('?', $lastString)[0] ?? '';
            if (empty($mid)) continue;

            $upUser = new UpUser();
            $upUser->mid = $mid;
            $upUser->timed_status = $timedStatus;
            $upUser->save();
            $addMidArr[] = $mid;
        }
        //Push a queue and obtain the main information of the UP asynchronous
        $this->queue->push(new UpUserInfoRecordJob([
            'up_user_mid' => $addMidArr,
        ]));

        if ($isExistEmptyUrl) return $this->successByMessage('Entry the success of the UP owner, and some URL entries are empty entry failed');

        return $this->successByMessage('Entrying UP Lord success');
    }

    /**
     * Get the UP user search list
     * @RequestMapping(path="up_user_search", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function upUserSearchList()
    {
        $mid = $this->request->input('mid') ?? '';
        $upUserQuery = $this->upUser->newQuery();
        if (!empty($mid)) $upUserQuery->where('mid', $mid);

        $list = $upUserQuery->limit(10)->orderBy('created_at')->get()->toArray();
        foreach ($list as $key => $value) {
            $list[$key]['name'] = $value['name'] . '(' . $value['mid'] . ')';
        }
        return $this->success([
            'list' => $list,
        ]);
    }

    /**
     * Get up UP user list
     * @RequestMapping(path="up_user", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function upUserList()
    {
        $mid = $this->request->input('mid') ?? '';
        $name =  $this->request->input('name') ?? '';
        $timedStatus =  $this->request->input('time_status') ?? '';

        $upUserQuery = $this->upUser->newQuery();
        if (!empty($mid)) $upUserQuery->where('mid', $mid);
        if (!empty($name)) $upUserQuery->where('name', 'like', '%' . $name . '%');
        if (strlen($timedStatus) > 0) $upUserQuery->where('timed_status', $timedStatus);

        $total = $upUserQuery->count();
        $this->pagingCondition($upUserQuery, $this->request->all());
        $list = $upUserQuery->orderBy('created_at', 'desc')->get()->toArray();

        return $this->success([
            'list' => $list,
            'total' => $total
        ]);
    }

    /**
     * Get up UP user list
     * @RequestMapping(path="sync_video_from_up_user", methods="post")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function syncVideoReportFromUpUser()
    {
        $mid = $this->request->input('mid') ?? '';
        if (empty($mid)) $this->throwExp(StatusCode::ERR_VALIDATION, 'Parameter error');

        $upUser = $this->upUser->newQuery()->where('mid', $mid)->first();
        if (empty($upUser)) $this->throwExp(StatusCode::ERR_EXCEPTION, 'Can\'t check the UP owner');

        //Push a queue, synchronize UP main video information
        $this->queue->push(new SyncVideoFromUpUserJob([
            'mid' => $mid,
        ]));

        return $this->successByMessage('In synchronous video... Please turn to the video list later to view');
    }

    /**
     * UP main chart trend
     * @RequestMapping(path="up_user_chart_trend", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function upUserChartTrend()
    {
        $mid = $this->request->input('mid') ?? '';
        $date = $this->request->input('date') ?? '';

        $upUserReportQuery = $this->upUserReport->newQuery();
        if (empty($mid)) $this->throwExp(StatusCode::ERR_VALIDATION, 'Please fill in the search UP master MID');
        if (!empty($mid)) $upUserReportQuery->where('mid', $mid);
        // Processing time
        $date = $date ?? [date('Y-m-d', strtotime('-6 days')), date('Y-m-d', time())];
        $beginTime = strtotime($date[0]);
        $endTime = strtotime($date[1]);
        $range = getRangeBetweenTime($beginTime, $endTime);
        if ($range > 7) $this->throwExp(StatusCode::ERR_EXCEPTION, 'The time range cannot exceed 7 days');
        $timestampList = [];
        for ($i = $beginTime; $i < $endTime; $i = $i + 3600) {
            $timestampList[] = $i;
        }
        $upUserReportQuery->where('time', '>=', $beginTime);
        $upUserReportQuery->where('time', '<=', $endTime);

        $rows = UpUserService::getInstance()->upUserChartTrend($upUserReportQuery, $timestampList);

        return $this->success([
            'rows' => $rows,
        ]);
    }

    /**
     * UP main chart trend
     * @RequestMapping(path="up_user_data_report", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function upUserDataReport()
    {
        $mid = $this->request->input('mid') ?? '';
        $date = $this->request->input('date') ?? '';

        $upUserReportQuery = $this->upUserReport->newQuery();
        if (empty($mid)) $this->throwExp(StatusCode::ERR_VALIDATION, 'Please fill in the search UP master MID');
        if (!empty($mid)) $upUserReportQuery->where('mid', $mid);
        // Processing time
        $date = empty($date) ? [date('Y-m-d', time()), date('Y-m-d', time())] : $date;
        $beginTime = strtotime($date[0]);
        $endTime = strtotime($date[1]) + 86400;

        $upUserReportQuery->where('time', '>=', $beginTime);
        $upUserReportQuery->where('time', '<=', $endTime);

        $total = $upUserReportQuery->count();
        $upUserReportQuery = $this->pagingCondition($upUserReportQuery, $this->request->all());

        $list = UpUserService::getInstance()->upUserDataReport($upUserReportQuery);
        return $this->success([
            'list' => $list,
            'total' => $total,
        ]);
    }
}

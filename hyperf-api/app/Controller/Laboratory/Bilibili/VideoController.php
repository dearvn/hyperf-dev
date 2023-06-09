<?php
declare(strict_types=1);

namespace App\Controller\Laboratory\Bilibili;

use App\Constants\StatusCode;
use App\Controller\AbstractController;
use App\Foundation\Annotation\Explanation;
use App\Foundation\Utils\Queue;
use App\Job\Bilibili\VideoInfoRecordJob;
use App\Model\Laboratory\Bilibili\Video;
use App\Model\Laboratory\Bilibili\VideoReport;
use App\Service\Laboratory\Bilibili\UpUserService;
use App\Service\Laboratory\Bilibili\VideoService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Middleware\RequestMiddleware;
use App\Middleware\PermissionMiddleware;

/**
 * Bilibili video
 * Class VideoController
 * @Controller(prefix="laboratory/bilibili_module/video")
 */
class VideoController extends AbstractController
{
    /**
     * @Inject()
     * @var Video
     */
    private $video;

    /**
     * @Inject()
     * @var VideoReport
     */
    private $videoReport;

    /**
     * @Inject()
     * @var Queue
     */
    private $queue;

    /**
     * @Explanation(content="Video entry")
     * @RequestMapping(path="video_add", methods="post")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     * @throws \Exception
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function videoAdd()
    {
        $videoInfo = $this->request->all()['video_info'] ?? [];
        if (empty($videoInfo)) $this->throwExp(StatusCode::ERR_VALIDATION, 'Video link information cannot be empty');

        //Whether there is an empty URL
        $isExistEmptyUrl = false;
        $addBVidArr = [];
        foreach ($videoInfo as $video) {
            $videoUrl = $video['video_url'] ?? '';
            $timedStatus = $video['timed_status'] ?? '';
            if (empty($videoUrl)) {
                $isExistEmptyUrl = true;
                continue;
            }
            $lastString = basename($videoUrl);
            $bvid = explode('?', $lastString)[0] ?? '';
            if (empty($bvid)) continue;

            $video = new Video();
            $video->bvid = $bvid;
            $video->timed_status = $timedStatus;
            $video->save();
            $addBVidArr[] = $bvid;
        }
        //Push a queue and obtain the main information of the UP asynchronous
        $this->queue->push(new VideoInfoRecordJob([
            'video_bvid' => $addBVidArr,
        ]));

        if ($isExistEmptyUrl) return $this->successByMessage('The enrollment video is successful, and some URL entries are empty entry failed');

        return $this->successByMessage('Entry video successfully');
    }

    /**
     * Get video title search list
     * @RequestMapping(path="video_title_search", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function videoTitleSearch()
    {
        $bvid = $this->request->input('bvid') ?? '';
        $videoQuery = $this->video->newQuery();
        if (!empty($bvid)) $videoQuery->where('bvid', $bvid);

        $list = $videoQuery->limit(10)->orderBy('public_time', 'desc')->get()->toArray();
        foreach ($list as $key => $value) {
            $list[$key]['title'] = $value['title'] . '(' . $value['bvid'] . ')';
        }
        return $this->success([
            'list' => $list,
        ]);
    }

    /**
     * Get up UP user list
     * @RequestMapping(path="video", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function videoList()
    {
        $bvid = $this->request->input('bvid') ?? '';
        $mid =  $this->request->input('mid') ?? '';
        $title =  $this->request->input('title') ?? '';
        $publicTime = $this->request->input('public_time') ?? '';

        $videoQuery = $this->video->newQuery();
        if (!empty($bvid)) $videoQuery->where('bvid', $mid);
        if (!empty($mid)) $videoQuery->where('mid', $mid);
        if (!empty($publicTime)) $videoQuery->whereBetween('public_time', [strtotime($publicTime[0]), strtotime($publicTime[1])]);
        if (!empty($title)) $videoQuery->where('title', 'like', '%' . $title . '%');

        $total = $videoQuery->count();
        $this->pagingCondition($videoQuery, $this->request->all());
        $list = $videoQuery->orderBy('created_at', 'desc')->get()->toArray();

        foreach ($list as $key => $value) {
            $list[$key]['public_time'] = date('Y-m-d H:i:s', $value['public_time']);
            $owner = json_decode($value['owner'], true);
            $list[$key]['name'] = $owner['name'];
            $list[$key]['duration'] = floor ($value['duration'] / 60) . '分' . $value['duration'] % 60 . '秒';
        }

        return $this->success([
            'list' => $list,
            'total' => $total
        ]);
    }

    /**
     * UP main chart trend
     * @RequestMapping(path="video_chart_trend", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function videoChartTrend()
    {
        $bvid = $this->request->input('bvid') ?? '';
        $date = $this->request->input('date') ?? '';

        $videoReportQuery = $this->videoReport->newQuery();
        if (empty($bvid)) $this->throwExp(StatusCode::ERR_VALIDATION, 'Please fill in the search video ID');
        $videoReportQuery->where('bvid', $bvid);
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
        $videoReportQuery->where('time', '>=', $beginTime);
        $videoReportQuery->where('time', '<=', $endTime);

        $rows = VideoService::getInstance()->videoChartTrend($videoReportQuery, $timestampList);

        return $this->success([
            'rows' => $rows,
        ]);
    }

    /**
     * UP main chart trend
     * @RequestMapping(path="video_data_report", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function upUserDataReport()
    {
        $bvid = $this->request->input('bvid') ?? '';
        $date = $this->request->input('date') ?? '';

        $videoReportQuery = $this->videoReport->newQuery();
        if (empty($bvid)) $this->throwExp(StatusCode::ERR_VALIDATION, 'Please fill in the video ID');
        $videoReportQuery->where('bvid', $bvid);
        // Processing time
        $date = empty($date) ? [date('Y-m-d', time()), date('Y-m-d', time())] : $date;
        $beginTime = strtotime($date[0]);
        $endTime = strtotime($date[1]) + 86400;

        $videoReportQuery->where('time', '>=', $beginTime);
        $videoReportQuery->where('time', '<=', $endTime);

        $total = $videoReportQuery->count();
        $videoReportQuery = $this->pagingCondition($videoReportQuery, $this->request->all());

        $list = VideoService::getInstance()->videoDataReport($videoReportQuery);
        return $this->success([
            'list' => $list,
            'total' => $total,
        ]);
    }
}

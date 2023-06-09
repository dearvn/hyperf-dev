<?php

declare(strict_types=1);

namespace App\Job\Bilibili;

use App\Foundation\Facades\Log;
use App\Foundation\Utils\Mail;
use App\Model\Auth\User;
use App\Model\Laboratory\Bilibili\Video;
use App\Service\Laboratory\Bilibili\UpUserService;
use App\Service\Laboratory\Bilibili\VideoService;
use Hyperf\AsyncQueue\Job;

/**
 * Synchronous UP main video
 * Class SyncVideoFromUpUserJob
 * @package App\Job\Bilibili
 * @Author YiYuan-Lin
 * @Date: 2021/8/25
 */
class SyncVideoFromUpUserJob extends Job
{
    public $params;
    /**
     * The number of retries after the failure of the task execution is the maximum execution number is $ maxattempts+1
     * @var int
     */
    protected $maxAttempts = 2;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function handle()
    {
        try {
           $videoInfo = VideoService::getInstance()->getVideoInfoFromUpUser($this->params['mid']);
           $videoBVid = array_column($videoInfo, 'bvid');

           foreach ($videoBVid as $bvid) {
               if (!empty(Video::query()->where('bvid', $bvid)->first())) continue;
               $video = new Video();
               $video->bvid = $bvid;
               $video->timed_status = Video::TIMED_STATUS_ON;
               $video->save();
           }

           VideoService::getInstance()->recordVideoInfoFromBilibili($videoBVid);
        } catch (\Exception $e) {
            Log::jobLog()->error($e->getMessage());
        }
    }

}
<?php

declare(strict_types=1);

namespace App\Job\Bilibili;

use App\Foundation\Facades\Log;
use App\Foundation\Utils\Mail;
use App\Model\Auth\User;
use App\Service\Laboratory\Bilibili\UpUserService;
use App\Service\Laboratory\Bilibili\VideoService;
use Hyperf\AsyncQueue\Job;

/**
 * Video data entry asynchronous queue
 * Class VideoInfoRecordJob
 * @package App\Job\Bilibili
 * @Author YiYuan-Lin
 * @Date: 2021/8/24
 */
class VideoInfoRecordJob extends Job
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
            VideoService::getInstance()->recordVideoInfoFromBilibili($this->params['video_bvid']);
        } catch (\Exception $e) {
            Log::jobLog()->error($e->getMessage());
        }
    }

}
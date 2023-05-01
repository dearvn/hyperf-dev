<?php

declare(strict_types=1);

namespace App\Job\Bilibili;

use App\Foundation\Facades\Log;
use App\Foundation\Utils\Mail;
use App\Model\Auth\User;
use App\Service\Laboratory\Bilibili\UpUserService;
use Hyperf\AsyncQueue\Job;

/**
 * New entry into UP main information entry
 * Class UpUserInfoRecordJob
 * @package App\Job\Bilibili
 * @Author YiYuan-Lin
 * @Date: 2021/8/20
 */
class UpUserInfoRecordJob extends Job
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
          UpUserService::getInstance()->recordUpUserInfoFromBilibili($this->params['up_user_mid']);
        } catch (\Exception $e) {
            Log::jobLog()->error($e->getMessage());
        }
    }

}
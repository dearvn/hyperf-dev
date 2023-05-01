<?php

declare(strict_types=1);

namespace App\Model\Setting;

use App\Foundation\Utils\Cron;
use App\Model\Model;

/**
 * Timing task log model class
 * Class TimedTaskLog
 * @package App\Model\System
 * @Author YiYuan-Lin
 * @Date: 2021/4/12
 */
class TimedTaskLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'timed_task_log';

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'default';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * Define the results of the running results
     */
    const SUCCESS_RESULT = 1;
    const FAILED_RESULT = 0;
}
<?php

declare(strict_types=1);

namespace App\Model\Laboratory\Bilibili;

use App\Model\Model;

/**
 * Video information table
 * Class Video
 * @package App\Model\Laboratory\Bilibili
 * @Author YiYuan-Lin
 * @Date: 2021/8/24
 */
class Video extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bili_video';

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
     * Timing task switch status
     */
    const TIMED_STATUS_ON = 1;
    const TIMED_STATUS_OFF = 0;
}
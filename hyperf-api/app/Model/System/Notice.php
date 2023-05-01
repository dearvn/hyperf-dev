<?php

declare(strict_types=1);

namespace App\Model\System;

use App\Model\Model;

/**
 * System notification form
 * Class Notice
 * @package App\Model\System
 * @Author YiYuan-Lin
 * @Date: 2021/3/3
 */
class Notice extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notice';

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
     * Publish state enumeration
     */
    const ON_STATUS = 1;
    const OFF_STATUS = 1;

    /**
     * Affrotten to User table
     * @return \Hyperf\Database\Model\Relations\BelongsTo
     */
    public function getUserName() {
        return $this->belongsTo('App\Model\Auth\User', 'user_id', 'id');
    }
}
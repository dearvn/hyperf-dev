<?php

declare(strict_types=1);

namespace App\Model\System;

use App\Model\Model;

/**
 * Parameter configuration
 * Class GlobalConfig
 * @package App\Model\System
 * @Author YiYuan-Lin
 * @Date: 2021/6/10
 */
class GlobalConfig extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'global_config';

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
     * Define parameter type enumeration
     */
    const TYPE_BY_TEXT = 'text';
    const TYPE_BY_JSON = 'json';
    const TYPE_BY_HTML = 'html';
    const TYPE_BY_BOOLEAN = 'boolean';

    /**
     * Get parameter information based on KeyName
     * @param string $keyName
     * @return array|\Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    public static function getOneByKeyName(string $keyName)
    {
        if (empty($keyName)) return [];

        return static::query()->where('key_name', $keyName)->first();
    }
}
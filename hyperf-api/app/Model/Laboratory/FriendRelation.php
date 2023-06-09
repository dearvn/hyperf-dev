<?php

declare(strict_types=1);

namespace App\Model\Laboratory;

use App\Model\Model;

/**
 * Friends relationship table
 * Class FriendRelation
 * @package App\Model\Laboratory
 * @Author YiYuan-Lin
 * @Date: 2021/7/9
 */
class FriendRelation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_friend_relation';

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
    *User online status
    *1: Online
    *0: not online
    */
    const FRIEND_ONLINE_STATUS = 1;
    const FRIEND_ONLINE_STATUS_NO = 0;

    /**
     * Affrotten to User table
     * @return \Hyperf\Database\Model\Relations\BelongsTo
     */
    public function getUser() {
        return $this->belongsTo('App\Model\Auth\User', 'friend_id', 'id');
    }

    /**
    *Get friend notes of friend relationship
    *@param int sender $fromUid
    *@param int receiver $toUid
    *@return \Hyperf\Utils\HigherOrderTapProxy|mixed|string|void
    */
    public static function getFriendRemarkNameById(int $fromUid, int $toUid)
    {
        if (empty($fromUid) || empty($toUid)) return '';

        return static::query()->where('uid', $toUid)->where('friend_id', $fromUid)->value('friend_remark');
    }
}
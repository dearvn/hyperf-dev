<?php

declare(strict_types=1);

namespace App\Model\Laboratory;

use App\Model\Model;
use Hyperf\Database\Model\SoftDeletes;

/**
 * Group chat and group membership relationship
 * Class GroupRelation
 * @package App\Model\Laboratory
 * @Author YiYuan-Lin
 * @Date: 2021/5/22
 */
class GroupRelation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_group_relation';

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
     * Group member level
     * 0: Group owner 1: Administrator 2: Ordinary member
     */
    const GROUP_MEMBER_LEVEL_LORD = 0;
    const GROUP_MEMBER_LEVEL_MANAGER= 1;
    const GROUP_MEMBER_LEVEL_MEMBER = 2;

    /**
     * Establish a group contact with users
     * @param int $uid
     * @param string $groupId
     * @param int $level
     * @return bool
     */
    public static function buildRelation(int $uid, string $groupId, int $level = self::GROUP_MEMBER_LEVEL_MEMBER)
    {
        $model = new static;
        $model->uid = $uid;
        $model->group_id = $groupId;
        $model->is_up = 0;
        $model->is_not_disturb = 0;
        $model->level = $level;
        return $model->save();
    }

    /**
     * Get user information
     * @return \Hyperf\Database\Model\Relations\BelongsTo
     */
    public function getUserInfo()
    {
        return $this->belongsTo("App\Model\Auth\User", 'uid', 'id');
    }

    /**
     * Get group chat level according to user ID
     * @param int $uid
     * @param string $groupId
     * @return \Hyperf\Utils\HigherOrderTapProxy|mixed|void
     */
    public static function getLevelById(int $uid, string $groupId)
    {
        if (empty($uid) || empty($groupId)) return false;
        return static::query()->where('uid', $uid)->where('group_id', $groupId)->value('level');
    }

    /**
     * Obtain users to join the group for other time according to ID
     * @param int $uid
     * @param string $groupId
     * @return \Hyperf\Utils\HigherOrderTapProxy|mixed|void
     */
    public static function getJoinDateById(int $uid, string $groupId)
    {
       return static::query()->where('uid', $uid)
           ->where('group_id', $groupId)
           ->value('created_at');
    }
}
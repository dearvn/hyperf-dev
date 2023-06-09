<?php

declare(strict_types=1);

namespace App\Model\Laboratory;

use App\Model\Model;
use Hyperf\Database\Model\SoftDeletes;

class Group extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_group';

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
     * 声明主键
     * @var
     */
    public $primaryKey = 'group_id';

    protected $keyType = 'string';

    /**
     * State whether the group logo
     * 1: Yes 0: No
     */
    const IS_GROUP_TYPE = 1;
    const IS_NOT_GROUP_TYPE = 0;

    /**
     * Declaration group talks about the default avatar
     */
    const DEFAULT_GROUP_AVATAR = 'https://hyperf-cms.oss-cn-guangzhou.aliyuncs.com/chat/group/composite_avatar/594f172886b3617e9cf8e29cd65f342b%20(2).png';
}
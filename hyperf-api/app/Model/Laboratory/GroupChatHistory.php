<?php

declare(strict_types=1);

namespace App\Model\Laboratory;

use App\Model\Model;
use Hyperf\Database\Model\SoftDeletes;

/**
 * Historical records of group chat news
 * Class GroupChatHistory
 * @package App\Model\Laboratory
 * @Author YiYuan-Lin
 * @Date: 2021/5/22
 */
class GroupChatHistory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_group_chat_history';

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
     * Group message type enumeration
     */
    const GROUP_CHAT_MESSAGE_TYPE_TEXT = 'text';
    const GROUP_CHAT_MESSAGE_TYPE_IMAGE = 'image';
    const GROUP_CHAT_MESSAGE_TYPE_FILE = 'file';
    const GROUP_CHAT_MESSAGE_TYPE_EVENT = 'event';
    const GROUP_CHAT_MESSAGE_TYPE_FORWARD = 'forward';

    /**
     * Group message status enumeration
     */
    const GROUP_CHAT_MESSAGE_STATUS_GOING = 'going';
    const GROUP_CHAT_MESSAGE_STATUS_SUCCEED = 'succeed';
    const GROUP_CHAT_MESSAGE_STATUS_FAILED = 'failed';

    /**
     * Add a group chat record
     * @param array $message
     * @param int $receptionState
     * @return bool
     */
    static function addMessage(array $message, int $receptionState = 0)
    {
        if (empty($message)) return false;

        $model = new self();
        $model->message_id = $message['id'];
        $model->type = $message['type'];
        $model->status = self::GROUP_CHAT_MESSAGE_STATUS_SUCCEED;
        $model->send_time = $message['sendTime'];
        $model->content = $message['content'];
        $model->file_size = $message['fileSize'] ?? 0;
        $model->file_ext = $message['fileExt'] ?? '';
        $model->file_name = $message['fileName'] ?? '';
        $model->to_group_id = $message['toContactId'];
        $model->from_uid = $message['fromUser']['id'] ?? 0;
        $model->reception_state = $receptionState;

        return $model->save();
    }

    /**
     * Get the album name
     * @return \Hyperf\Database\Model\Relations\BelongsTo
     */
    public function getFromUser()
    {
        return $this->belongsTo("App\Model\Auth\User", 'from_uid', 'id');
    }
}
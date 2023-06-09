<?php

declare(strict_types=1);

namespace App\Model\Laboratory;

use App\Model\Model;
use App\Pool\Redis;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * Friend message
 * Class FriendChatHistory
 * @package App\Model\Laboratory
 * @Author YiYuan-Lin
 * @Date: 2021/7/8
 */
class FriendChatHistory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_friend_chat_history';

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
     * Message acceptance status enumeration
     */
    const RECEPTION_STATE_YES = 1;
    const RECEPTION_STATE_NO = 0;

    /**
     * Group message type enumeration
     */
    const FRIEND_CHAT_MESSAGE_TYPE_TEXT = 'text';
    const FRIEND_CHAT_MESSAGE_TYPE_IMAGE = 'image';
    const FRIEND_CHAT_MESSAGE_TYPE_FILE = 'file';
    const FRIEND_CHAT_MESSAGE_TYPE_EVENT = 'event';
    const FRIEND_CHAT_MESSAGE_TYPE_FORWARD = 'forward';

    /**
     * Group message status enumeration
     */
    const FRIEND_CHAT_MESSAGE_STATUS_GOING = 'going';
    const FRIEND_CHAT_MESSAGE_STATUS_SUCCEED = 'succeed';
    const FRIEND_CHAT_MESSAGE_STATUS_FAILED = 'failed';

    /**
     * Friends message container
     */
    const FRIEND_MESSAGE_CONTAINER_REDIS_KEY = 'FRIEND_MESSAGE_CONTAINER_BY_USER_';


    /**
     * Add chat history
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
        $model->status = 'succeed';
        $model->send_time = $message['sendTime'];
        $model->content = $message['content'];
        $model->file_size = $message['fileSize'] ?? 0;
        $model->file_name = $message['fileName'] ?? '';
        $model->file_ext = $message['fileExt'] ?? '';
        $model->to_uid = $message['toContactId'];
        $model->from_uid = $message['fromUser']['id'] ?? 0;
        $model->reception_state = $receptionState;
        $model->save();

        //Add a message to a friend container
        //self::addMessageToContainer($message['id']);

        return true;
    }

    /**
     * Add messages to personal message containers
     * @param $messageId
     * @return bool
     */
//    static function addMessageToContainer(string $messageId)
//    {
//        if (empty($messageId)) return false;
//
//        $messageInfo = static::query()->where('message_id', $messageId)->first();
//        if (!empty($messageId)) {
//            Redis::getInstance()->hset(self::FRIEND_MESSAGE_CONTAINER_REDIS_KEY . $messageInfo['from_uid'], $messageId, json_encode($messageInfo));
//            Redis::getInstance()->hset(self::FRIEND_MESSAGE_CONTAINER_REDIS_KEY . $messageInfo['to_uid'], $messageId, json_encode($messageInfo));
//        }
//
//        return true;
//    }
}
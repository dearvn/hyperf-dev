<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Constants\Laboratory;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * Class ChatRedisKey
 * The key of the chat system Redis item
 * @Constants
 * @package App\Constants
 * @Author YiYuan-Lin
 * @Date: 2021/3/12
 */
class ChatRedisKey extends AbstractConstants
{
    /**
     * @Message("Online user and FD binding relationship")
     */
    const ONLINE_USER_FD_KEY = 'online_user_fd_list';

    /**
     * @Message("FD binding relationship with online users")
     */
    const ONLINE_FD_USER_KEY = 'online_fd_user_list';

    /**
     * @Message("Unexpected chat records")
     */
    const GROUP_CHAT_UNREAD_MESSAGE_BY_USER = 'group_chat_unread_message_user_';

}

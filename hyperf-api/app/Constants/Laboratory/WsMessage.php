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
 * Class WsMessage
 * Chat system related enumeration
 * @Constants
 * @package App\Constants
 * @Author YiYuan-Lin
 * @Date: 2021/3/12
 */
class WsMessage extends AbstractConstants
{
    /**
     * @Message("The type of information is initialization")
     */
    const MESSAGE_TYPE_INIT = 'init';

    /**
     * @Message("User goes online")
     */
    const FRIEND_ONLINE_MESSAGE = 'friend_online_message';

    /**
     * @Message("User offline")
     */
    const FRIEND_OFFLINE_MESSAGE = 'friend_offline_message';

    /**
     * @Message("Pull friends information")
     */
    const MESSAGE_TYPE_PULL_FRIEND_MESSAGE = 'friend_history_message';

    /**
     * @Message("Pulling group information")
     */
    const MESSAGE_TYPE_PULL_GROUP_MESSAGE = 'group_history_message';

    /**
     * @Message("Friends withdraw information")
     */
    const MESSAGE_TYPE_FRIEND_WITHDRAW_MESSAGE = 'friend_withdraw_message';

    /**
     * @Message("Group chat withdrawal message")
     */
    const MESSAGE_TYPE_GROUP_WITHDRAW_MESSAGE = 'group_withdraw_message';

    /**
     * @Message("New users join")
     */
    const MESSAGE_TYPE_NEW_FRIEND_JOIN = 'new_friend_join_message';

    /**
     * @Message("User delete")
     */
    const MESSAGE_TYPE_FRIEND_DELETE = 'friend_delete_message';
}

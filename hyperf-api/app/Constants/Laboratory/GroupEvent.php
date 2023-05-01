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
 * Message event enumeration
 * Class GroupEvent
 * @Constants
 * @package App\Constants\Laboratory
 * @Author YiYuan-Lin
 * @Date: 2021/5/8
 */
class GroupEvent extends AbstractConstants
{
    /**
     * @Message("Create event")
     */
    const CREATE_GROUP_EVENT = 'create_group';

    /**
     * @Message("Modify group operation")
     */
    const EDIT_GROUP_EVENT = 'edit_group';

    /**
     * @Message("Newly joined the team incident")
     */
    const NEW_MEMBER_JOIN_GROUP_EVENT = 'new_member_join_group';

    /**
     * @Message("Group members retreat")
     */
    const GROUP_MEMBER_EXIT_EVENT = 'group_member_exit';

    /**
     * @Message("Delete the group")
     */
    const DELETE_GROUP_MEMBER_EVENT = 'delete_group_member';

    /**
     * @Message("Change the group level event")
     */
    const CHANGE_GROUP_MEMBER_LEVEL_EVENT = 'change_group_member_level';

    /**
     * @Message("Disopoly group chat incident")
     */
    const DELETE_GROUP_EVENT = 'delete_group';

    /**
     * @Message("Update group chat avatar")
     */
    const CHANGE_GROUP_AVATAR = 'change_group_avatar';

    /**
     * @Message("Forward information")
     */
    const FORWARD_MESSAGE = 'forward_message';
}

<?php
declare(strict_types = 1);

namespace App\Task\Laboratory;

use App\Constants\Laboratory\ChatRedisKey;
use App\Constants\Laboratory\GroupEvent;
use App\Foundation\Utils\GroupAvatar;
use App\Model\Auth\User;
use App\Model\Laboratory\FriendChatHistory;
use App\Model\Laboratory\Group;
use App\Model\Laboratory\GroupChatHistory;
use App\Model\Laboratory\GroupRelation;
use App\Pool\Redis;
use App\Service\Laboratory\GroupService;
use App\Service\Laboratory\MessageService;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Task\Annotation\Task;

/**
 * Group message transmission asynchronous task
 * Class GroupWsTask
 * @package App\Task
 * @Author YiYuan-Lin
 * @Date: 2021/3/23
 */
class GroupWsTask
{
    /**
     * @Inject()
     * @var \Hyperf\WebSocketServer\Sender
     */
    private $sender;

    /**
     * @Task()
     * Create event
     * @param array $groupInfo
     * @return bool
     */
    public function createGroupEvent(array $groupInfo)
    {
        if (empty($groupInfo)) return false;

        $uidFdList = GroupService::getInstance()->getOnlineGroupMemberFd($groupInfo['group_id']);
        $message = [];
        $message['status'] = GroupChatHistory::GROUP_CHAT_MESSAGE_STATUS_SUCCEED;
        $message['type'] = GroupChatHistory::GROUP_CHAT_MESSAGE_TYPE_EVENT;
        $message['sendTime'] = time() * 1000;

        $groupInfoTemp = [];
        $groupInfoTemp['id'] = $groupInfo['group_id'];
        $groupInfoTemp['displayName'] = $groupInfo['group_name'];
        $groupInfoTemp['avatar'] = $groupInfo['avatar'];
        $groupInfoTemp['size'] = $groupInfo['size'];
        $groupInfoTemp['content'] = '';
        $groupInfoTemp['index'] = "[0]group chat";
        $groupInfoTemp['introduction'] = $groupInfo['introduction'];
        $groupInfoTemp['validation'] = $groupInfo['validation'];
        $groupInfoTemp['is_group'] = Group::IS_GROUP_TYPE;
        $groupInfoTemp['member_total'] = 0;

        //Get the member information
        $groupMembersUidList = GroupRelation::query()->where('group_id', $groupInfo['group_id'])->orderBy('level', 'asc')->pluck('uid')->toArray();
        if (!empty($groupMembersUidList)) {
            $groupMembersList = User::query()->select('a.id', 'a.desc', 'a.avatar', 'b.level')
                ->from('users as a')
                ->whereIn('a.id', $groupMembersUidList)
                ->leftJoin('ct_group_relation as b', 'a.id', 'b.uid')
                ->where('b.group_id', $groupInfo['group_id'])
                ->orderBy(Db::raw('FIND_IN_SET(a.id, "' . implode(",", $groupMembersUidList) . '"' . ")"))
                ->get()->toArray();
            $groupInfoTemp['group_member'] = $groupMembersList;
            $groupInfoTemp['member_total'] = count($groupMembersList);
        }
        $message['group_info'] = $groupInfoTemp;

        foreach ($uidFdList as $key => $value) {
            $sendMessage['event'] = GroupEvent::CREATE_GROUP_EVENT;
            $message['group_info']['level'] = GroupRelation::getLevelById($value['uid'], $groupInfo['group_id']);
            $sendMessage['message'] = $message;
            $this->sender->push((int) $value['fd'], json_encode($sendMessage));
        }
        return true;
    }

    /**
     * @Task()
     * Invite users to enter the group incident
     * @param array $groupInfo
     * @param array $contactIdList
     * @return bool
     */
    public function groupMemberJoinEvent(array $groupInfo, array $contactIdList)
    {
        if (empty($groupInfo)) return false;

        //Obtain all online users of the group according to the group ID
        $uidFdList = GroupService::getInstance()->getOnlineGroupMemberFd($groupInfo['group_id']);
        $message = [];
        $message['status'] = GroupChatHistory::GROUP_CHAT_MESSAGE_STATUS_SUCCEED;
        $message['type'] = GroupChatHistory::GROUP_CHAT_MESSAGE_TYPE_EVENT;
        $message['sendTime'] = time() * 1000;
        $message['newJoinGroupMember'] = $contactIdList;

        $groupInfoTemp = [];
        $groupInfoTemp['id'] = $groupInfo['group_id'];
        $groupInfoTemp['displayName'] = $groupInfo['group_name'];
        $groupInfoTemp['avatar'] = $groupInfo['avatar'];
        $groupInfoTemp['size'] = $groupInfo['size'];
        $groupInfoTemp['content'] = '';
        $groupInfoTemp['index'] = "[0]group chat";
        $groupInfoTemp['introduction'] = $groupInfo['introduction'];
        $groupInfoTemp['validation'] = $groupInfo['validation'];
        $groupInfoTemp['is_group'] = Group::IS_GROUP_TYPE;
        $groupInfoTemp['member_total'] = 0;

        //Get the member information
        $groupMembersUidList = GroupRelation::query()->where('group_id', $groupInfo['group_id'])->orderBy('level', 'asc')->pluck('uid')->toArray();
        if (!empty($groupMembersUidList)) {
            $groupMembersList = User::query()->select('a.id', 'a.desc', 'a.avatar', 'b.level')
                ->from('users as a')
                ->whereIn('a.id', $groupMembersUidList)
                ->leftJoin('ct_group_relation as b', 'a.id', 'b.uid')
                ->where('b.group_id', $groupInfo['group_id'])
                ->orderBy(Db::raw('FIND_IN_SET(a.id, "' . implode(",", $groupMembersUidList) . '"' . ")"))
                ->get()->toArray();
            $groupInfoTemp['group_member'] = $groupMembersList;
            $groupInfoTemp['member_total'] = count($groupMembersList);
        }
        $message['group_info'] = $groupInfoTemp;

        foreach ($uidFdList as $key => $value) {
            $sendMessage['event'] = GroupEvent::NEW_MEMBER_JOIN_GROUP_EVENT;
            $sendMessage['message'] = $message;
            $this->sender->push((int) $value['fd'], json_encode($sendMessage));
        }
        return true;
    }

    /**
     * @Task()
     * Group members retreat
     * @param array $groupInfo
     * @param array $userInfo
     * @param string $event
     * @return bool
     */
    public function groupMemberExitEvent(array $groupInfo, array $userInfo, string $event)
    {
        if (empty($groupInfo)) return false;
        if (empty($userInfo)) return false;
        $message = [];
        $content = $event == GroupEvent::GROUP_MEMBER_EXIT_EVENT ? $userInfo['desc'] . ' His exit group chat' : $userInfo['desc'] . ' Kick out of group chat';
        $message['id'] = generate_rand_id();
        $message['status'] = GroupChatHistory::GROUP_CHAT_MESSAGE_STATUS_SUCCEED;
        $message['type'] = GroupChatHistory::GROUP_CHAT_MESSAGE_TYPE_EVENT;
        $message['uid'] = $userInfo['id'];
        $message['sendTime'] = time() * 1000;
        $message['toContactId'] = $groupInfo['group_id'];
        $message['content'] = $content ?? '';
        $message['displayName'] = $groupInfo['group_name'] ?? '';
        $message['group_member'] = [];
        $message['member_total'] = [];

        $groupMembersUidList = GroupRelation::query()
            ->where('group_id', $groupInfo['group_id'])
            ->where('uid', '!=', $userInfo['id'])
            ->orderBy('level', 'asc')->pluck('uid')
            ->toArray();
        //Determine whether a group member is empty, and obtain group member information
        if (!empty($groupMembersUidList)) {
            $groupMembersList = User::query()->select('a.id', 'a.desc', 'a.avatar', 'b.level')
                ->from('users as a')
                ->whereIn('a.id', $groupMembersUidList)
                ->leftJoin('ct_group_relation as b', 'a.id', 'b.uid')
                ->where('b.group_id', $groupInfo['group_id'])
                ->orderBy(Db::raw('FIND_IN_SET(a.id, "' . implode(",", $groupMembersUidList) . '"' . ")"))
                ->get()->toArray();
            $message['group_member'] = $groupMembersList;
            $message['member_total'] = count($groupMembersList);
        }
        //Get all online users of the group according to the group id
        $this->sendMessage($groupInfo['group_id'], $message, $event);
        //Delete the binding relationship between the group and the user board
        GroupRelation::query()->where('group_id', $groupInfo['group_id'])->where('uid', $userInfo['id'])->delete();
        return true;
    }

    /**
     * @Task()
     * Change the team level event
     * @param array $groupInfo
     * @param array $userInfo
     * @param int $changeLevel
     * @return bool
     */
    public function changeGroupMemberLevel(array $groupInfo, array $userInfo, int $changeLevel)
    {
        if (empty($groupInfo)) return false;
        if (empty($userInfo)) return false;
        if (empty($changeLevel)) return false;
        //change datasheet
        GroupRelation::query()->where('group_id', $groupInfo['group_id'])->where('uid', $userInfo['id'])->update(['level' => $changeLevel]);
        $message = [];
        $content = $changeLevel == GroupRelation::GROUP_MEMBER_LEVEL_MANAGER ? $userInfo['desc'] . ' Set as an administrator' : $userInfo['desc'] . ' Dedicated administrator';
        $message['id'] = generate_rand_id();
        $message['status'] = GroupChatHistory::GROUP_CHAT_MESSAGE_STATUS_SUCCEED;
        $message['type'] = GroupChatHistory::GROUP_CHAT_MESSAGE_TYPE_EVENT;
        $message['uid'] = $userInfo['id'];
        $message['sendTime'] = time() * 1000;
        $message['toContactId'] = $groupInfo['group_id'];
        $message['content'] = $content ?? '';
        $message['displayName'] = $groupInfo['group_name'] ?? '';
        $message['level'] = GroupRelation::getLevelById($userInfo['id'], $groupInfo['group_id']);
        $message['group_member'] = [];
        $message['member_total'] = [];

        $groupMembersUidList = GroupRelation::query()
            ->where('group_id', $groupInfo['group_id'])
            ->orderBy('level', 'asc')->pluck('uid')
            ->toArray();
        //Determine whether a group member is empty, and obtain group member information
        if (!empty($groupMembersUidList)) {
            $groupMembersList = User::query()->select('a.id', 'a.desc', 'a.avatar', 'b.level')
                ->from('users as a')
                ->whereIn('a.id', $groupMembersUidList)
                ->leftJoin('ct_group_relation as b', 'a.id', 'b.uid')
                ->where('b.group_id', $groupInfo['group_id'])
                ->orderBy(Db::raw('FIND_IN_SET(a.id, "' . implode(",", $groupMembersUidList) . '"' . ")"))
                ->get()->toArray();
            $message['group_member'] = $groupMembersList;
            $message['member_total'] = count($groupMembersList);
        }
        //Get all online users of the group according to the group id
        $this->sendMessage($groupInfo['group_id'], $message, GroupEvent::CHANGE_GROUP_MEMBER_LEVEL_EVENT);
        return true;
    }

    /**
     * Disopoly group chat
     * @param array $groupInfo
     * @param array $userInfo
     * @return bool
     */
    public function deleteGroup(array $groupInfo, array $userInfo)
    {

        if (empty($groupInfo)) return false;
        if (empty($userInfo)) return false;
        $message = [];
        $content = $userInfo['desc'] . ' Has been dissolved "' . $groupInfo['group_name'] . '" The group chat';
        $message['id'] = generate_rand_id();
        $message['status'] = GroupChatHistory::GROUP_CHAT_MESSAGE_STATUS_SUCCEED;
        $message['type'] = GroupChatHistory::GROUP_CHAT_MESSAGE_TYPE_EVENT;
        $message['uid'] = $userInfo['id'];
        $message['sendTime'] = time() * 1000;
        $message['toContactId'] = $groupInfo['group_id'];
        $message['content'] = $content ?? '';
        $message['displayName'] = $groupInfo['group_name'] ?? '';
        //Notify all group users
        $this->sendMessage($groupInfo['group_id'], $message, GroupEvent::DELETE_GROUP_EVENT);

        //Delete all content about the group owner
        Group::query()->where('group_id', $groupInfo['group_id'])->delete();
        GroupRelation::query()->where('group_id', $groupInfo['group_id'])->delete();
        GroupChatHistory::query()->where('to_group_id', $groupInfo['group_id'])->delete();
        return true;
    }

    /**
     * Update group chat avatar
     * @param array $groupInfo
     * @return bool
     * @throws \League\Flysystem\FileExistsException
     */
    public function changeGroupAvatar(array $groupInfo)
    {
        if (empty($groupInfo)) return false;
        //If it is not the default avatar, it will not be replaced
        if (!strstr($groupInfo['avatar'], 'composite_avatar')) return false;
        $message = [];
        $message['id'] = generate_rand_id();
        $message['status'] = GroupChatHistory::GROUP_CHAT_MESSAGE_STATUS_SUCCEED;
        $message['type'] = GroupChatHistory::GROUP_CHAT_MESSAGE_TYPE_EVENT;
        $message['sendTime'] = time() * 1000;
        $message['toContactId'] = $groupInfo['group_id'];

        $uidList = GroupRelation::query()->where('group_id', $groupInfo['group_id'])->orderBy('created_at', 'desc')->limit(9)->pluck('uid')->toArray();
        $picList = User::query()->whereIn('id', $uidList)->pluck('avatar')->toArray();
        GroupAvatar::init($picList, false, 'chat/group/composite_avatar');
        $message['avatar'] = GroupAvatar::build();
        Group::query()->where('group_id', $groupInfo['group_id'])->update(['avatar' => $message['avatar']]);

        $this->sendMessage($groupInfo['group_id'], $message, GroupEvent::CHANGE_GROUP_AVATAR, false);
        return true;
    }

    /**
     * Merge forwarding information
     * @param array $groupInfo
     * @param array $user
     * @param string $content
     * @return bool
     */
    function mergeForwardMessage(array $groupInfo, array $user, string $content)
    {
        //Add chat history
        $message = [];
        $message['id'] = generate_rand_id();
        $message['from_uid'] = $user['id'];
        $message['to_group_id'] = $groupInfo['group_id'];
        $message['type'] = GroupChatHistory::GROUP_CHAT_MESSAGE_TYPE_FORWARD;
        $message['status'] = GroupChatHistory::GROUP_CHAT_MESSAGE_STATUS_SUCCEED;
        $message['sendTime'] = time() * 1000;
        $message['content'] = $content;
        $message['toContactId'] = $groupInfo['group_id'];
        $message['fromUser'] = $user;

        //Obtain non -online users, and add it to unrelated historical news
        $unOnlineUidList = GroupService::getInstance()->getUnOnlineGroupMember($groupInfo['group_id']);
        foreach ($unOnlineUidList as $uid) {
            Redis::getInstance()->sAdd(ChatRedisKey::GROUP_CHAT_UNREAD_MESSAGE_BY_USER . $uid, $groupInfo['group_id']);
        }
        $this->sendMessage($groupInfo['group_id'], $message, GroupEvent::FORWARD_MESSAGE);
        return true;
    }

    /**
     * Forward information one by one
     * @param array $groupInfo
     * @param array $user
     * @param array $content
     * @return bool
     */
    function forwardMessage(array $groupInfo, array $user, array $content)
    {
        if (is_array($content)) {
            foreach ($content as $item) {
                $messageSource = $item['is_group'] == true ? GroupChatHistory::query()->where('message_id', $item['id'])->first() : FriendChatHistory::query()->where('message_id', $item['id'])->first();
                $messageSource = objToArray($messageSource);
                if (empty($messageSource)) continue;
                //Add chat history
                $message = [];
                $message['id'] = generate_rand_id();
                $message['from_uid'] = $user['id'];
                $message['to_group_id'] = $groupInfo['group_id'];
                $message['type'] = $messageSource['type'];
                $message['status'] = GroupChatHistory::GROUP_CHAT_MESSAGE_STATUS_SUCCEED;
                $message['sendTime'] = time() * 1000;
                $message['content'] = $messageSource['content'];
                $message['toContactId'] = $groupInfo['group_id'];
                $message['fromUser'] = $user;
                $message['fileSize'] = $messageSource['file_size'];
                $message['fileName'] = $messageSource['file_name'];
                $message['fileExt'] = $messageSource['file_ext'];

                //Obtain non -online users, and add it to unrelated historical news
                $unOnlineUidList = GroupService::getInstance()->getUnOnlineGroupMember($groupInfo['group_id']);
                foreach ($unOnlineUidList as $uid) {
                    Redis::getInstance()->sAdd(ChatRedisKey::GROUP_CHAT_UNREAD_MESSAGE_BY_USER . $uid, $groupInfo['group_id']);
                }
                $this->sendMessage($groupInfo['group_id'], $message, GroupEvent::FORWARD_MESSAGE);
            }
            return true;
        }
    }

    /**
     * Group message sending
     * @param string $groupId
     * @param array $message
     * @param string $event
     * @param bool $isAddChatHistory
     * @return bool
     */
    public function sendMessage(string $groupId, array $message, string $event = '', bool $isAddChatHistory = true )
    {
        if (empty($groupId || empty($message))) return false;
        if (empty($message['fromUser'])) {
            $message['fromUser']['id'] = 0;
            $message['fromUser']['displayName'] = 'system notification';
        }
        $message['isGroup'] = true;

        //Add chat history
        if ($isAddChatHistory) GroupChatHistory::addMessage($message, 1);
        $uidFdList = GroupService::getInstance()->getOnlineGroupMemberFd($groupId);
        if ($message['type'] == GroupChatHistory::GROUP_CHAT_MESSAGE_TYPE_FORWARD) $message['content'] = MessageService::getInstance()->formatForwardMessage($message['content'], $message['fromUser']);
        foreach ($uidFdList as $key => $value) {
            $sendMessage['event'] = $event;
            $sendMessage['message'] = $message;
            $this->sender->push((int) $value['fd'], json_encode($sendMessage));
        }
        return true;
    }
}

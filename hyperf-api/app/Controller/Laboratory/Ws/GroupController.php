<?php
declare(strict_types = 1);

namespace App\Controller\Laboratory\Ws;

use App\Constants\Laboratory\ChatRedisKey;
use App\Constants\Laboratory\GroupEvent;
use App\Constants\Laboratory\WsMessage;
use App\Controller\AbstractController;
use App\Foundation\Facades\MessageParser;
use App\Model\Auth\User;
use App\Model\Laboratory\FriendChatHistory;
use App\Model\Laboratory\Group;
use App\Model\Laboratory\GroupChatHistory;
use App\Model\Laboratory\GroupRelation;
use App\Pool\Redis;
use App\Service\Laboratory\GroupService;
use App\Service\Laboratory\MessageService;
use App\Task\Laboratory\GroupWsTask;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;

/**
 * Group chat controller
 * Class GroupController
 * @package App\Controller\Laboratory\Ws
 * @Controller(prefix="group",server="ws")
 */
class GroupController extends AbstractController
{
    /**
     * send Message
     * @RequestMapping(path="send_message",methods="GET")
     */
    public function sendMessage()
    {
        $chatMessage = MessageParser::decode(conGet('chat_message'));
        $contactData = $chatMessage['message'];

        //Add chat history
        GroupChatHistory::addMessage($contactData);
        $fdList = GroupService::getInstance()->getOnlineGroupMemberFd($contactData['toContactId'], $contactData, true);
        $fdList = array_column($fdList, 'fd');

        //Obtain non -online users, and add it to unrelated historical news
        $unOnlineUidList = GroupService::getInstance()->getUnOnlineGroupMember($contactData['toContactId']);
        foreach ($unOnlineUidList as $uid) {
            Redis::getInstance()->sAdd(ChatRedisKey::GROUP_CHAT_UNREAD_MESSAGE_BY_USER . $uid, $contactData['id']);
        }
        $contactData['status'] = GroupChatHistory::GROUP_CHAT_MESSAGE_STATUS_SUCCEED;
        unset($contactData['fromUser']['unread']);
        unset($contactData['fromUser']['lastSendTime']);
        unset($contactData['fromUser']['lastContent']);

        //Formatal repost message type
        if ($contactData['type'] == GroupChatHistory::GROUP_CHAT_MESSAGE_TYPE_FORWARD) $contactData['content'] = GroupService::getInstance()->formatForwardMessage($contactData['content'], $contactData['fromUser']);
        return [
            'message_data' => [
                'event' => '',
                'message' => [
                    'id' => $contactData['id'],
                    'status' => GroupChatHistory::GROUP_CHAT_MESSAGE_STATUS_SUCCEED,
                    'type' => $contactData['type'],
                    'sendTime' => $contactData['sendTime'],
                    'content' => $contactData['content'],
                    'toContactId' =>$contactData['toContactId'],
                    'fromUser' => $contactData['fromUser'],
                    'isGroup' => true,
                ],
            ],
            'fd' => $fdList
        ];
    }

    /**
     * Pull the message
     * @RequestMapping(path="pull_message",methods="GET")
     */
    public function pullMessage()
    {
        $chatMessage = MessageParser::decode(conGet('chat_message'));
        $contactData = $chatMessage['message'];
        $userFd = Redis::getInstance()->hget(ChatRedisKey::ONLINE_USER_FD_KEY, (string) $contactData['user_id']);

        $userJoinGroupDate = GroupRelation::getJoinDateById($contactData['user_id'], $contactData['contact_id']);
        $messageList = GroupChatHistory::query()->where('to_group_id', $contactData['contact_id'])
            ->where('created_at', '>=', $userJoinGroupDate)
            ->orderBy('id', 'desc')->limit(300)
            ->get()->toArray();
        $messageList = array_reverse($messageList);

        $list = [];
        foreach ($messageList as $key => $value) {
            $temp = [
                'id' => $value['message_id'],
                'status' => $value['status'],
                'type' => $value['type'],
                'sendTime' => intval($value['send_time']),
                'content' => $value['content'],
                'toContactId' => $value['to_group_id'],
                'fileSize' => $value['file_size'],
                'fileName' => $value['file_name'],
                'isGroup' => true,
            ];
            if ($value['from_uid'] != 0) $temp['fromUser'] = [
                'id' => $value['from_uid'],
                'avatar' => User::query()->where('id', $value['from_uid'])->value('avatar') ?? '',
                'displayName' => User::query()->where('id', $value['from_uid'])->value('desc') ?? '',
            ];
            //Formatal forwarding type message type
            if ($value['type'] == GroupChatHistory::GROUP_CHAT_MESSAGE_TYPE_FORWARD) $temp['content'] = MessageService::getInstance()->formatForwardMessage($value['content'], $temp['fromUser']);
            $list[] = $temp;
        }
        return [
            'message_data' => [
                'group_history_message' => $list,
                'event' => WsMessage::MESSAGE_TYPE_PULL_GROUP_MESSAGE
            ],
            'fd' => $userFd,
        ];
    }

    /**
     * Creation group
     * @RequestMapping(path="create_group",methods="POST")
     */
    public function createGroup()
    {
        $chatMessage = MessageParser::decode(conGet('chat_message'));
        $contactData = $chatMessage['message'];

        $groupInsertData = [];
        $groupInsertData['group_id'] = getRandStr(16);
        $groupInsertData['uid'] = $contactData['creator']['id'];
        $groupInsertData['group_name'] = $contactData['name'];
        $groupInsertData['avatar'] = empty($contactData['avatar']) ? Group::DEFAULT_GROUP_AVATAR : $contactData['avatar'];
        $groupInsertData['size'] = $contactData['size'] ?? 200;
        $groupInsertData['introduction'] = $contactData['introduction'] ?? '';
        $groupInsertData['validation'] = $contactData['validation'] ?? 0;
        $groupInsertData['created_at'] = date('Y-m-d H:i:s');
        $groupInsertData['updated_at'] = date('Y-m-d H:i:s');
        Group::query()->insert($groupInsertData);
        GroupRelation::buildRelation($groupInsertData['uid'], $groupInsertData['group_id'], GroupRelation::GROUP_MEMBER_LEVEL_LORD);

        if (!empty($contactData['checkedContacts'])) {
            $contactIdList = array_column($contactData['checkedContacts'], 'id');
            if (!empty($contactIdList)) {
                foreach ($contactIdList as $contactId) {
                    GroupRelation::buildRelation($contactId, $groupInsertData['group_id']);
                }
            }
        }
        //Push the creation group event
        $this->container->get(GroupWsTask::class)->createGroupEvent($groupInsertData);
        if (!empty($contactIdList)) {
            //Push new members into the group notice
            $newMemberJoinMessage = [];
            $joinUserInfo = User::query()->whereIn('id', $contactIdList)->pluck('desc')->toArray();
            $content = join(' , ', $joinUserInfo) . ' Join group chat';
            $newMemberJoinMessage['id'] = generate_rand_id();
            $newMemberJoinMessage['status'] = GroupChatHistory::GROUP_CHAT_MESSAGE_STATUS_SUCCEED;
            $newMemberJoinMessage['type'] = GroupChatHistory::GROUP_CHAT_MESSAGE_TYPE_EVENT;
            $newMemberJoinMessage['sendTime'] = time() * 1000;
            $newMemberJoinMessage['toContactId'] = $groupInsertData['group_id'];
            $newMemberJoinMessage['content'] = $content ?? '';
            $this->container->get(GroupWsTask::class)->sendMessage($groupInsertData['group_id'], $newMemberJoinMessage);
            $this->container->get(GroupWsTask::class)->changeGroupAvatar($groupInsertData);
        }
    }

    /**
     * Members to modify the group information operation
     * @RequestMapping(path="edit_group",methods="POST")
     */
    public function editGroup()
    {
        $chatMessage = MessageParser::decode(conGet('chat_message'));
        $contactData = $chatMessage['message'];

        if (empty($contactData['group_id'])) return false;
        $groupInfo = Group::findById($contactData['group_id']);
        $userInfo = User::findById($contactData['uid']);
        if (empty($groupInfo)) return false;
        $groupInfo->avatar = $contactData['avatar'] ?? '';
        $groupInfo->group_name = $contactData['group_name'] ?? '';
        $groupInfo->introduction = $contactData['introduction'] ?? '';
        $groupInfo->size = $contactData['size'] ?? 200;
        $groupInfo->validation = $contactData['validation'] ?? 0;
        $groupInfo->save();

        $message = [];
        $content = $userInfo['desc'] . ' Modified group information';
        $message['id'] = generate_rand_id();
        $message['status'] = GroupChatHistory::GROUP_CHAT_MESSAGE_STATUS_SUCCEED;
        $message['type'] = GroupChatHistory::GROUP_CHAT_MESSAGE_TYPE_EVENT;
        $message['sendTime'] = time() * 1000;
        $message['toContactId'] = $groupInfo['group_id'];
        $message['content'] = $content ?? '';
        $message['group_info'] = $groupInfo;

        //Notify all group members in the group to modify the group data operation
        $this->container->get(GroupWsTask::class)->sendMessage($contactData['group_id'], $message, GroupEvent::EDIT_GROUP_EVENT);
        return true;
    }

    /**
     * Invitation team
     * @RequestMapping(path="invite_group_member",methods="POST")
     */
    public function inviteGroupMember()
    {
        //TODO You need to verify whether the group exists, and there may be possible group members to invite users to delete the group operation when they invite users
        $chatMessage = MessageParser::decode(conGet('chat_message'));
        $contactData = $chatMessage['message'];

        $groupInfo = Group::findById($contactData['id'])->toArray();
        if (empty($groupInfo)) return false;

        if (!empty($contactData['newJoinGroupMember'])) {
            $contactIdList = array_column($contactData['newJoinGroupMember'], 'id');
            if (!empty($contactIdList)) {
                foreach ($contactIdList as $contactId) {
                    GroupRelation::buildRelation($contactId, $contactData['id']);
                }
            }
        }
        if (!empty($contactIdList)) {
            //Push new members into the group notice
            $newMemberJoinMessage = [];
            $content = join(User::query()->whereIn('id', $contactIdList)->pluck('desc')->toArray(), ' , ') . ' Join group chat';
            $newMemberJoinMessage['id'] = generate_rand_id();
            $newMemberJoinMessage['status'] = GroupChatHistory::GROUP_CHAT_MESSAGE_STATUS_SUCCEED;
            $newMemberJoinMessage['type'] = GroupChatHistory::GROUP_CHAT_MESSAGE_TYPE_EVENT;
            $newMemberJoinMessage['sendTime'] = time() * 1000;
            $newMemberJoinMessage['toContactId'] = $contactData['id'];
            $newMemberJoinMessage['content'] = $content ?? '';
            //First notify the user to join the group operation, and send the additional message event to join the group message event
            $this->container->get(GroupWsTask::class)->groupMemberJoinEvent($groupInfo, $contactIdList);
            $this->container->get(GroupWsTask::class)->sendMessage($contactData['id'], $newMemberJoinMessage);
            $this->container->get(GroupWsTask::class)->changeGroupAvatar($groupInfo);
        }
        return true;
    }

    /**
     * Members retreat to group operation
     * @RequestMapping(path="exit_group",methods="POST")
     */
    public function exitGroup()
    {
        $chatMessage = MessageParser::decode(conGet('chat_message'));
        $contactData = $chatMessage['message'];

        if (empty($contactData['group_id'])) return false;
        if (empty($contactData['uid'])) return false;
        $groupInfo = Group::findById($contactData['group_id'])->toArray();
        $userInfo = User::findById($contactData['uid'])->toArray();
        if (empty($groupInfo)) return false;
        if (empty($userInfo)) return false;

        //Notify the user to refund the group incident
        $this->container->get(GroupWsTask::class)->groupMemberExitEvent($groupInfo, $userInfo, GroupEvent::GROUP_MEMBER_EXIT_EVENT );
        $this->container->get(GroupWsTask::class)->changeGroupAvatar($groupInfo);
        return true;
    }

    /**
     * Remove the user's group chat incident
     * @RequestMapping(path="delete_group_member",methods="POST")
     */
    public function deleteGroupMember()
    {
        $chatMessage = MessageParser::decode(conGet('chat_message'));
        $contactData = $chatMessage['message'];

        if (empty($contactData['group_id'])) return false;
        if (empty($contactData['uid'])) return false;
        $groupInfo = Group::findById($contactData['group_id'])->toArray();
        $userInfo = User::findById($contactData['uid'])->toArray();
        if (empty($groupInfo)) return false;
        if (empty($userInfo)) return false;

        //Notify the user to refund the group incident
        $this->container->get(GroupWsTask::class)->groupMemberExitEvent($groupInfo, $userInfo, GroupEvent::DELETE_GROUP_MEMBER_EVENT);
        $this->container->get(GroupWsTask::class)->changeGroupAvatar($groupInfo);
        return true;
    }

    /**
     * Modify user -level events
     * @RequestMapping(path="change_group_member_level",methods="POST")
     */
    public function changeGroupMemberLevel()
    {
        $chatMessage = MessageParser::decode(conGet('chat_message'));
        $contactData = $chatMessage['message'];

        if (empty($contactData['group_id'])) return false;
        if (empty($contactData['uid'])) return false;
        $groupInfo = Group::findById($contactData['group_id'])->toArray();
        $userInfo = User::findById($contactData['uid'])->toArray();
        if (empty($groupInfo)) return false;
        if (empty($userInfo)) return false;

        $changeLevel = $contactData['level'] == GroupRelation::GROUP_MEMBER_LEVEL_MANAGER ? GroupRelation::GROUP_MEMBER_LEVEL_MEMBER : GroupRelation::GROUP_MEMBER_LEVEL_MANAGER;
        $this->container->get(GroupWsTask::class)->changeGroupMemberLevel($groupInfo, $userInfo, $changeLevel);
        return true;
    }

    /**
     * Disopoly group chat
     * @RequestMapping(path="delete_group",methods="POST")
     */
    public function deleteGroup()
    {
        $chatMessage = MessageParser::decode(conGet('chat_message'));
        $contactData = $chatMessage['message'];

        if (empty($contactData['group_id'])) return false;
        if (empty($contactData['uid'])) return false;
        $groupInfo = Group::findById($contactData['group_id'])->toArray();
        $userInfo = User::findById($contactData['uid'])->toArray();

        if (empty($groupInfo)) return false;
        if (empty($userInfo)) return false;
        Db::beginTransaction();
        $this->container->get(GroupWsTask::class)->deleteGroup($groupInfo, $userInfo);
        Db::commit();
        return true;
    }
}


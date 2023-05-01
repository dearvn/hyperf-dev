<?php
namespace App\Service\Laboratory;

use App\Constants\Laboratory\ChatRedisKey;
use App\Constants\Laboratory\WsMessage;
use App\Foundation\Traits\Singleton;
use App\Model\Auth\User;
use App\Model\Laboratory\FriendChatHistory;
use App\Model\Laboratory\FriendGroup;
use App\Model\Laboratory\FriendRelation;
use App\Model\Laboratory\Group;
use App\Model\Laboratory\GroupChatHistory;
use App\Model\Laboratory\GroupRelation;
use App\Pool\Redis;
use App\Service\BaseService;
use Hyperf\DbConnection\Db;

/**
 * Chat initialization service
 * Class MessageService
 * @package App\Service\Setting
 * @Author YiYuan-Lin
 * @Date: 2021/3/12
 */
class InitService extends BaseService
{
    use Singleton;

    /**
     * Get initialization chat information
     * @return array
     */
    public function initialization() : array
    {
        $returnUserInfo = [];
        $userInfo = conGet('user_info');

        $returnUserInfo['id'] = $userInfo['id'];
        $returnUserInfo['displayName'] = $userInfo['desc'];
        $returnUserInfo['avatar'] = $userInfo['avatar'];

        //Get user contact
        $userListQuery = FriendRelation::query();
        $userListQuery->where('uid',  $userInfo['id']);
        $userListQuery->with('getUser:id,desc,avatar');
        $userList = $userListQuery->get()->toArray();

        //Get the user's group
        $friendGroup[0] = [
            'id' => 0,
            'uid' => $userInfo['id'],
            'friend_group_name' => 'my good friend',
        ];
        $friendGroup += FriendGroup::query()->select('id', 'friend_group_name as label')->where('uid', $userInfo['id'])->orderBy('sort', 'asc')->get()->toArray();
        $friendGroup = array_column($friendGroup, null, 'id');
        $userContactList = [];
        foreach ($userList as $key => $val) {
            $fd = Redis::getInstance()->hget(ChatRedisKey::ONLINE_USER_FD_KEY, (string) $val['id']);
            $unreadMessageInfo = $this->getUnReadMessageByUser($val, $userInfo);
            $userContactList[] = [
                'id' => $val['get_user']['id'],
                'is_group' => Group::IS_NOT_GROUP_TYPE,
                'displayName' => empty($val['friend_remark']) ? $val['get_user']['desc'] : $val['friend_remark'],
                'avatar' => $val['get_user']['avatar'],
                'index' => $friendGroup[$val['friend_group']]['friend_group_name'],
                'friend_group' => $val['friend_group'],
                'unread' => $unreadMessageInfo['unread'] ?? 0,
                'status' => empty($fd) ? FriendRelation::FRIEND_ONLINE_STATUS_NO : FriendRelation::FRIEND_ONLINE_STATUS,
                'lastContent' => $unreadMessageInfo['lastContent'] ?? '',
                'lastContentType' => $unreadMessageInfo['lastContentType'] ?? '',
                'lastSendTime' => $unreadMessageInfo['lastSendTime'] ?? getMillisecond(),
            ];
        }
        //Obtain a user group
        $userHasGroupId = GroupRelation::query()->where('uid', $userInfo['id'])->pluck('group_id');
        $groupList = Group::query()->whereIn('group_id', $userHasGroupId)->get()->toArray();
        $userGroupList = [];
        foreach ($groupList as $key => $val) {
            $unreadMessageInfo = $this->getUnReadMessageByGroup($val, $userInfo);
            $groupMembersUidList = GroupRelation::query()->where('group_id', $val['group_id'])->orderBy('level', 'asc')->pluck('uid')->toArray();
            $temp = [
                'id' => $val['group_id'],
                'is_group' => Group::IS_GROUP_TYPE,
                'displayName' => $val['group_name'],
                'avatar' => $val['avatar'],
                'introduction' => $val['introduction'],
                'validation' => $val['validation'],
                'size' => $val['size'],
                'uid' => $val['uid'],
                'index' => "[0]群聊",
                'unread' => $unreadMessageInfo['unread'] ?? 0,
                'member_total' => 0,
                'level' => GroupRelation::getLevelById($userInfo['id'], $val['group_id']),
                'lastContent' => $unreadMessageInfo['lastContent'] ?? '',
                'lastContentType' => $unreadMessageInfo['lastContentType'] ?? '',
                'lastSendTime' => $unreadMessageInfo['lastSendTime'] ?? getMillisecond(),
            ];
            //The judgment team is empty, and the information of the members of the group is obtained
            if (!empty($groupMembersUidList)) {
                $groupMembersList = User::query()->select('a.id', 'a.desc', 'a.avatar', 'b.level')
                    ->from('users as a')
                    ->whereIn('a.id', $groupMembersUidList)
                    ->leftJoin('ct_group_relation as b', 'a.id', 'b.uid')
                    ->where('b.group_id', $val['group_id'])
                    ->orderBy(Db::raw('FIND_IN_SET(a.id, "' . implode(",", $groupMembersUidList) . '"' . ")"))
                    ->get()->toArray();
                $temp['group_member'] = $groupMembersList;
                $temp['member_total'] = count($groupMembersList);
            }
            $userGroupList[] = $temp;
        }
        return [
            'event' => WsMessage::MESSAGE_TYPE_INIT,
            'user_info' => $returnUserInfo,
            'user_contact' => $userContactList,
            'friend_group' =>$friendGroup,
            'user_group' => $userGroupList
        ];
    }

    /**
     * According to the user's obtaining the last information and unreasonable information
     * @param array $user
     * @param array $currentUserInfo
     * @return array
     */
    private function getUnReadMessageByUser(array $user, array $currentUserInfo) : array
    {
        if (empty($user)) return [];

        $lastMessage = FriendChatHistory::query()
            ->where(function ($query) use ($currentUserInfo, $user) {
                $query->where('from_uid', $currentUserInfo['id'])->where('to_uid', $user['get_user']['id']);
            })->orWhere(function ($query) use ($currentUserInfo, $user) {
                $query->where('from_uid', $user['get_user']['id'])->where('to_uid', $currentUserInfo['id']);
            })
            ->orderBy('send_time', 'desc')
            ->first();

        $unread = FriendChatHistory::query()
            ->where(function ($query) use ($currentUserInfo, $user) {
                $query->where('from_uid', $user['get_user']['id'])->where('to_uid', $currentUserInfo['id'])
                    ->where('reception_state', FriendChatHistory::RECEPTION_STATE_NO);
            })
            ->orderBy('send_time', 'desc')
            ->count();

        return [
            'unread' => $unread,
            'lastContent' => $lastMessage['content'] ?? '',
            'lastSendTime' => intval($lastMessage['send_time'] ?? 0),
            'lastContentType' => $lastMessage['type'] ?? ''
        ];
    }

    /**
     * Obtain the last information and unreasonable information according to the group
     * @param array $groupInfo
     * @param array $currentUserInfo
     * @return array
     */
    private function getUnReadMessageByGroup(array $groupInfo, array $currentUserInfo) : array
    {
        if (empty($currentUserInfo)) return [];
        $unread = Redis::getInstance()->sCard(ChatRedisKey::GROUP_CHAT_UNREAD_MESSAGE_BY_USER . $currentUserInfo['id']);
        Redis::getInstance()->del(ChatRedisKey::GROUP_CHAT_UNREAD_MESSAGE_BY_USER . $currentUserInfo['id']);

        $lastMessage = GroupChatHistory::query()->where('to_group_id', $groupInfo['group_id'])->orderBy('send_time', 'desc')->first();
        return [
            'unread' => $unread,
            'lastContent' => $lastMessage['content'] ?? '',
            'lastSendTime' => intval($lastMessage['send_time'] ?? 0) ,
            'lastContentType' => $lastMessage['type'] ?? ''
        ];
    }
}
<?php
namespace App\Service\Laboratory;

use App\Constants\Laboratory\ChatRedisKey;
use App\Foundation\Traits\Singleton;
use App\Model\Auth\User;
use App\Model\Laboratory\GroupChatHistory;
use App\Model\Laboratory\GroupRelation;
use App\Pool\Redis;
use App\Service\BaseService;

/**
 * Group message service
 * Class GroupService
 * @package App\Service\Laboratory
 * @Author YiYuan-Lin
 * @Date: 2021/5/8
 */
class GroupService extends BaseService
{
    use Singleton;

    /**
     * Obtain online team members FD according to the group ID
     * @param string $groupId
     * @param array $contactData
     * @param bool $isExcludeSelf
     * @return array
     */
    public function getOnlineGroupMemberFd(string $groupId, array $contactData = [], bool $isExcludeSelf = false) : array
    {
        if (empty($groupId)) return [];

        //Get all the list of team members
        $uidList = GroupRelation::query()->where('group_id', $groupId)->pluck('uid');
        $fdList = [];
        foreach ($uidList as $uid) {
            //Determine if it is excluded, only the other members of the group FD
            if ($isExcludeSelf && !empty($contactData) && $uid == $contactData['fromUser']['id']) continue;
            if (!empty($fd = Redis::getInstance()->hget(ChatRedisKey::ONLINE_USER_FD_KEY, (string) $uid))) array_push($fdList, [
                'uid' => $uid,
                'fd' => $fd
            ]);
        }
        return $fdList;
    }

    /**
     * Obtain the list of non -online users
     * @param string $groupId
     * @return array
     */
    public function getUnOnlineGroupMember(string $groupId) : array
    {
        if (empty($groupId)) return [];

        //Get all the list of team members
        $uidList = GroupRelation::query()->where('group_id', $groupId)->pluck('uid')->toArray();
        foreach ($uidList as $key => $value) {
            //Determine whether it is online, and eliminate online
            if (!empty(Redis::getInstance()->hget(ChatRedisKey::ONLINE_USER_FD_KEY, (string) $value))) {
                unset($uidList[$key]);
            }
        }
        return $uidList;
    }
}
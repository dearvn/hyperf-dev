<?php
namespace App\Service\Laboratory\Bilibili;

use App\Foundation\Traits\Singleton;
use App\Model\Laboratory\Bilibili\UpUser;
use App\Service\BaseService;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;

class UpUserService extends BaseService
{
    use Singleton;

    /**
     * UP main information (name, gender, avatar, description, personal certification information, large member status, live broadcast room address, preview map, title, room number, viewer number, live broadcast state [open/closing], etc.)
     * @var string
     */
    private $upUserInfoApi = 'https://api.bilibili.com/x/space/acc/info?mid=';

    /**
     * Number of up the number of fans, the number of attention
     * @var string
     */
    private $upUserStatApi  = 'https://api.bilibili.com/x/relation/stat?vmid=';

    /**
     * UP master total playback number, total column viewing number
     * @var string
     */
    private $upUserUpStatApi  = 'https://api.bilibili.com/x/space/upstat?mid=';

    /**
     * UP main charging information (the number of monthly charging person, monthly charging user, total charging person)
     * @var string
     */
    private $upUserElecApi  = 'https://api.bilibili.com/x/ugcpay-rank/elec/month/up?up_mid=';

    /**
     * Record UP main data
     * @param array $upUserMid
     * @return bool
     * @throws \Exception
     */
    public function recordUpUserInfoFromBilibili(array $upUserMid) : bool
    {
        if (empty($upUserMid)) return false;

        foreach ($upUserMid as $mid) {
            $upUserInfo = $this->getUpUserInfoFromBilibili($mid);
            if (!empty($upUserInfo)) {
                $updateData['name'] = $upUserInfo['name'] ?? '';
                $updateData['sex'] = $upUserInfo['sex'] ?? '';
                $updateData['sign'] = $upUserInfo['sign'] ?? '';
                $updateData['face'] = $upUserInfo['face'] ?? '';
                $updateData['level'] = $upUserInfo['level'] ?? '';
                $updateData['top_photo'] = $upUserInfo['top_photo'] ?? '';
                $updateData['birthday'] = $upUserInfo['birthday'] ?? '';
                $updateData['following'] = $upUserInfo['following'] ?? 0;
                $updateData['follower'] = $upUserInfo['follower'] ?? 0;
                $updateData['video_play'] = $upUserInfo['video_play'] ?? 0;
                $updateData['readling'] = $upUserInfo['readling'] ?? 0;
                $updateData['likes'] = $upUserInfo['likes'] ?? 0;
                $updateData['recharge_month'] = $upUserInfo['count'] ?? 0;
                $updateData['recharge_total'] = $upUserInfo['total'] ?? 0;
                $updateData['live_room_info'] = $upUserInfo['live_room_info'] ?? '';
                $updateData['updated_at'] = date('Y-m-d H:i:s');
                UpUser::where('mid', $mid)->update($updateData);
            }
        }

        return true;
    }

    /**
     * Get UP main data from Bilibili from Bilibili
     * @param string $upUserMid
     * @return array
     * @throws \Exception
     */
    public function getUpUserInfoFromBilibili(string $upUserMid) : array
    {
        if (empty($upUserMid)) return [];
        $upUserInfo = curl_get($this->upUserInfoApi . $upUserMid);
        $upUserStat = curl_get($this->upUserStatApi . $upUserMid);
        //This interface is more special, you need to use cookies
        $upUserUpStat = curl_get($this->upUserUpStatApi . $upUserMid, [], [],  config('bilibili.cookie'));
        $upUserElec = curl_get($this->upUserElecApi . $upUserMid);

        return  [
            'name' => $upUserInfo['data']['name'] ?? '',
            'sex' => $upUserInfo['data']['sex'] ?? '',
            'sign' => $upUserInfo['data']['sign'] ?? '',
            'face' => $upUserInfo['data']['face'] ?? '',
            'level' => $upUserInfo['data']['level'] ?? '',
            'top_photo' => $upUserInfo['data']['top_photo'] ?? '',
            'birthday' => $upUserInfo['data']['birthday'] ?? '',
            'following' => $upUserStat['data']['following'] ?? 0,
            'follower' => $upUserStat['data']['follower'] ?? 0,
            'video_play' => $upUserUpStat['data']['archive']['view'] ?? 0,
            'readling' => $upUserUpStat['data']['article']['view'] ?? 0,
            'likes' => $upUserUpStat['data']['likes'] ?? 0,
            'recharge_month' => $upUserElec['data']['count'] ?? 0,
            'recharge_total' => $upUserElec['data']['total'] ?? 0,
            'live_room_info' => empty($upUserInfo['data']['live_room']) ? '' : json_encode($upUserInfo['data']['live_room']),
        ];
    }

    /**
     * Get the UP main data trend chart
     * @param Builder $query
     * @param array $timestampList
     * @return array
     */
    public function upUserChartTrend(Builder $query, array $timestampList = []) : array
    {
        $query->orderBy('time');
        $upUserReport = $query->get([
            'time', 'following', 'follower', 'video_play', 'readling', 'likes', 'recharge_total'
        ])->toArray();
        $minUpUserReport = $query->select(Db::raw(
            'min(following) as following, 
                   min(follower) as follower,
                   min(video_play) as video_play,
                   min(readling) as readling,
                   min(likes) as likes,
                   min(recharge_total) as recharge_total
        '))->first()->toArray();
        $upUserReport = array_column($upUserReport, null, 'time');

        $rows = [];
        $list = [];
        foreach ($timestampList as $ts) {
            $dataDate = date('Y-m-d', $ts);
            if (!empty($upUserReport[$ts]['following'])) $list['following'][$dataDate][$ts] = intval($upUserReport[$ts]['following']);
            if (!empty($upUserReport[$ts]['follower'])) $list['follower'][$dataDate][$ts] = intval($upUserReport[$ts]['follower']);
            if (!empty($upUserReport[$ts]['likes'])) $list['likes'][$dataDate][$ts] = intval($upUserReport[$ts]['likes']);
            if (!empty($upUserReport[$ts]['recharge_total'])) $list['recharge_total'][$dataDate][$ts] = intval($upUserReport[$ts]['recharge_total']);
            if (!empty($upUserReport[$ts]['video_play'])) $list['video_play'][$dataDate][$ts] = intval($upUserReport[$ts]['video_play']);
            if (!empty($upUserReport[$ts]['readling'])) $list['readling'][$dataDate][$ts] = intval($upUserReport[$ts]['readling']);
        }

        foreach ($list as $key => $value) {
            $rows[$key]['columns'] = ['time'];
            for ($i = 0; $i < 24; $i ++) {
                $temp = [];
                foreach ($value as $k => $v) {
                    $temp['time'] = $i;
                    //如果某个时间点数据为空，则拿其上个时间点数据作为补充
                    $temp[$k] = $value[$k][strtotime($k) + ($i * 3600)] ?? '';
                    if ($i == 0) {
                        $rows[$key]['columns'][] = $k;
                    }
                }
                $rows[$key]['rows'][] = $temp;
            }
        }
        $rows['follower']['label'] = 'Number of fans';
        $rows['follower']['desc'] = 'As of the current time (hour), the number of fans within the time range is compared.';
        $rows['follower']['chartSettings']['min'] = [$minUpUserReport['follower']];
        $rows['likes']['label'] = 'Praise';
        $rows['likes']['desc'] = 'As of the current time (hour), the number of changes in the number of praise within the time range is compared.';
        $rows['likes']['chartSettings']['min'] = [$minUpUserReport['likes']];
        $rows['recharge_total']['label'] = 'Total charging number';
        $rows['recharge_total']['desc'] = 'As of the current time (hour), the total charging number of the total charging number of the time range is compared.';
        $rows['recharge_total']['chartSettings']['min'] = [$minUpUserReport['recharge_total']];
        $rows['following']['label'] = 'Pay attention';
        $rows['following']['desc'] = 'As of the current time (hours), the real -time attention of the number of changes in the time range is compared.';
        $rows['following']['chartSettings']['min'] = [$minUpUserReport['following']];
        $rows['video_play']['label'] = 'Video playback';
        $rows['video_play']['desc'] = 'As of the current time (hour), the video playback trend of video playback is compared.';
        $rows['video_play']['chartSettings']['min'] = [$minUpUserReport['video_play']];
        $rows['readling']['label'] = 'Number of reading';
        $rows['readling']['desc'] = 'As of the current time (hours), the changes in the number of reading within the time range are compared.';
        $rows['readling']['chartSettings']['min'] = [$minUpUserReport['readling']];

        return $rows;
    }

    /**
     * Get the main data report of the UP
     * @param Builder $query
     * @return array
     */
    public function upUserDataReport(Builder $query) : array
    {
        $query->orderBy('time', 'desc');
        $upUserReport = $query->get([
            'time', 'following', 'follower', 'video_play', 'readling', 'likes', 'recharge_total', 'recharge_month'
        ])->toArray();

        foreach ($upUserReport as $key => $value) {
            $upUserReport[$key]['time'] = date('Y-m-d H:i', $value['time']);

            if (empty($upUserReport[$key + 1])) continue;
            $upUserReport[$key]['following_trend'] = $value['following'] - $upUserReport[$key + 1]['following'];
            $upUserReport[$key]['follower_trend'] = $value['follower'] - $upUserReport[$key + 1]['follower'];
            $upUserReport[$key]['video_play_trend'] = $value['video_play'] - $upUserReport[$key + 1]['video_play'];
            $upUserReport[$key]['readling_trend'] = $value['readling'] - $upUserReport[$key + 1]['readling'];
            $upUserReport[$key]['likes_trend'] = $value['likes'] - $upUserReport[$key + 1]['likes'];
            $upUserReport[$key]['recharge_total_trend'] = $value['recharge_total'] - $upUserReport[$key + 1]['recharge_total'];
            $upUserReport[$key]['recharge_month_trend'] = $value['recharge_month'] - $upUserReport[$key + 1]['recharge_month'];
        }

        return $upUserReport;
    }
}
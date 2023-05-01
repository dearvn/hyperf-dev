<?php

namespace App\Service\Laboratory\Bilibili;

use App\Foundation\Facades\Log;
use App\Foundation\Traits\Singleton;
use App\Model\Laboratory\Bilibili\Video;
use App\Service\BaseService;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;

class VideoService extends BaseService
{
    use Singleton;

    /**
     * Get the video AV number (including the basic information of the video, if the BV corresponding video belongs to a series of videos, API will list all series of videos)
     * @var string
     */
    private $videoInfoApi = 'https://api.bilibili.com/x/web-interface/view?bvid=';

    /**
     * Get all the video data of the user based on the user MID
     * @var
     */
    private $videoInfoFromMidApi = 'https://api.bilibili.com/x/space/arc/search?mid=';

    /**
     * Update video data
     * @param array $videoBVid
     * @return bool
     * @throws \Exception
     */
    public function recordVideoInfoFromBilibili(array $videoBVid): bool
    {
        go(function () use ($videoBVid) {
            if (empty($videoBVid)) return false;

            foreach ($videoBVid as $bvid) {
                $videoInfo = $this->getVideoInfoFromBilibili($bvid);
                Log::codeDebug()->info(json_encode($videoInfo));
                if (!empty($videoInfo)) {
                    $updateData['mid'] = $videoInfo['mid'] ?? '';
                    $updateData['cover'] = $videoInfo['cover'] ?? '';
                    $updateData['title'] = $videoInfo['title'] ?? '';
                    $updateData['public_time'] = $videoInfo['public_time'] ?? '';
                    $updateData['desc'] = $videoInfo['desc'] ?? '';
                    $updateData['duration'] = $videoInfo['duration'] ?? '';
                    $updateData['view'] = $videoInfo['view'] ?? 0;
                    $updateData['danmaku'] = $videoInfo['danmaku'] ?? 0;
                    $updateData['reply'] = $videoInfo['reply'] ?? 0;
                    $updateData['favorite'] = $videoInfo['favorite'] ?? 0;
                    $updateData['coin'] = $videoInfo['coin'] ?? 0;
                    $updateData['likes'] = $videoInfo['likes'] ?? 0;
                    $updateData['dislike'] = $videoInfo['dislike'] ?? 0;
                    $updateData['owner'] = !empty($videoInfo['owner']) ? json_encode($videoInfo['owner']) : '';
                    $updateData['updated_at'] = date('Y-m-d H:i:s');
                    Video::where('bvid', $bvid)->update($updateData);
                }
            }
            return true;
        });
        return true;
    }

    /**
     * Get video data from Bilibili based on BVID
     * @param string $videoBVid
     * @return array
     * @throws \Exception
     */
    public function getVideoInfoFromBilibili(string $videoBVid): array
    {
        if (empty($videoBVid)) return [];
        $videoInfo = curl_get($this->videoInfoApi . $videoBVid);

        return [
            'bvid'        => $videoInfo['data']['bvid'] ?? '',
            'mid'         => $videoInfo['data']['owner']['mid'] ?? '',
            'owner'       => $videoInfo['data']['owner'] ?? [],
            'cover'       => $videoInfo['data']['pic'] ?? '',
            'title'       => $videoInfo['data']['title'] ?? '',
            'public_time' => $videoInfo['data']['pubdate'] ?? 0,
            'desc'        => $videoInfo['data']['desc'] ?? '',
            'duration'    => $videoInfo['data']['duration'] ?? 0,
            'view'        => $videoInfo['data']['stat']['view'] ?? 0,
            'danmaku'     => $videoInfo['data']['stat']['danmaku'] ?? 0,
            'reply'       => $videoInfo['data']['stat']['reply'] ?? 0,
            'favorite'    => $videoInfo['data']['stat']['favorite'] ?? 0,
            'coin'        => $videoInfo['data']['stat']['coin'] ?? 0,
            'likes'       => $videoInfo['data']['stat']['like'] ?? 0,
            'dislike'     => $videoInfo['data']['stat']['dislike'] ?? 0,
        ];
    }

    /**
     * Obtain video list based on UP main ID
     * @param string $mid
     * @return array
     * @throws \Exception
     */
    public function getVideoInfoFromUpUser(string $mid): array
    {
        if (empty($mid)) return [];
        $videoList = [];
        //Get the video data for the first time
        $videoInfo = curl_get($this->videoInfoFromMidApi . $mid . '&pn=1&&ps=30');

        if (!empty($videoInfo['data']['list']['vlist'])) {
            $videoList = array_merge($videoList, $videoInfo['data']['list']['vlist']);
            $pageInfo = $videoInfo['data']['page'];

            if ($pageInfo['count'] > 30) {
                for ($i = 2; $i <= ceil($pageInfo['count'] / $pageInfo['ps']); $i++) {
                    $temp = curl_get($this->videoInfoFromMidApi . $mid . '&pn=' . $i . '&&ps=30');
                    $videoList = array_merge($videoList, $temp['data']['list']['vlist']);
                }
            }
        }

        return $videoList;
    }

    /**
     * Video data trend chart
     * @param Builder $query
     * @param array $timestampList
     * @return array
     */
    public function videoChartTrend(Builder $query, array $timestampList = []): array
    {
        $query->orderBy('time');
        $videoReport = $query->get([
            'time',
            'view',
            'danmaku',
            'reply',
            'favorite',
            'coin',
            'likes',
            'dislike'
        ])->toArray();
        $minVideoReport = $query->select(Db::raw(
            'min(view) as view, 
                   min(danmaku) as danmaku,
                   min(reply) as reply,
                   min(favorite) as favorite,
                   min(coin) as coin,
                   min(likes) as likes,
                   min(dislike) as dislike
        '))->first()->toArray();
        $videoReport = array_column($videoReport, null, 'time');

        $rows = [];
        $list = [];
        foreach ($timestampList as $ts) {
            $dataDate = date('Y-m-d', $ts);
            if (!empty($videoReport[$ts]['view'])) $list['view'][$dataDate][$ts] = intval($videoReport[$ts]['view']);
            if (!empty($videoReport[$ts]['likes'])) $list['likes'][$dataDate][$ts] = intval($videoReport[$ts]['likes']);
            if (!empty($videoReport[$ts]['favorite'])) $list['favorite'][$dataDate][$ts] = intval($videoReport[$ts]['favorite']);
            if (!empty($videoReport[$ts]['coin'])) $list['coin'][$dataDate][$ts] = intval($videoReport[$ts]['coin']);
            if (!empty($videoReport[$ts]['danmaku'])) $list['danmaku'][$dataDate][$ts] = intval($videoReport[$ts]['danmaku']);
            if (!empty($videoReport[$ts]['reply'])) $list['reply'][$dataDate][$ts] = intval($videoReport[$ts]['reply']);
            if (!empty($videoReport[$ts]['dislike'])) $list['dislike'][$dataDate][$ts] = intval($videoReport[$ts]['dislike']);
        }

        foreach ($list as $key => $value) {
            $rows[$key]['columns'] = ['time'];
            for ($i = 0; $i < 24; $i++) {
                $temp = [];
                foreach ($value as $k => $v) {
                    $temp['time'] = $i;
                    //If the data is empty at a certain time point, take the data of the last time point as a supplement
                    $temp[$k] = $value[$k][strtotime($k) + ($i * 3600)] ?? '';
                    if ($i == 0) {
                        $rows[$key]['columns'][] = $k;
                    }
                }
                $rows[$key]['rows'][] = $temp;
            }
        }
        $rows['view']['label'] = 'Video playback';
        $rows['view']['desc'] = 'As of the current time (hour), the changes in the number of video playback in the time range are compared.';
        $rows['view']['chartSettings']['min'] = [$minVideoReport['view']];
        $rows['danmaku']['label'] = 'Number of barrage';
        $rows['danmaku']['desc'] = 'As of the current time (hour), the number of barrage changes within the time range is compared.';
        $rows['danmaku']['chartSettings']['min'] = [$minVideoReport['danmaku']];
        $rows['reply']['label'] = 'Number of comments';
        $rows['reply']['desc'] = 'As of the current time (hour), the changes in the number of comments within the time range are compared.';
        $rows['reply']['chartSettings']['min'] = [$minVideoReport['reply']];
        $rows['favorite']['label'] = 'Collecting number';
        $rows['favorite']['desc'] = 'As of the current time (hour), the real -time collection trend of the real -time collection of time is compared.';
        $rows['favorite']['chartSettings']['min'] = [$minVideoReport['favorite']];
        $rows['coin']['label'] = 'Coin number';
        $rows['coin']['desc'] = 'As of the current time (hours), the number of coins within the time range is comparison.';
        $rows['coin']['chartSettings']['min'] = [$minVideoReport['coin']];
        $rows['likes']['label'] = 'Praise';
        $rows['likes']['desc'] = 'As of the current time (hour), the number of praise trends within the time range is compared.';
        $rows['likes']['chartSettings']['min'] = [$minVideoReport['likes']];

        return $rows;
    }

    /**
     * Get video data report
     * @param Builder $query
     * @return array
     */
    public function videoDataReport(Builder $query): array
    {
        $query->orderBy('time', 'desc');
        $videoReport = $query->get([
            'time',
            'view',
            'danmaku',
            'reply',
            'favorite',
            'coin',
            'likes',
            'dislike'
        ])->toArray();

        foreach ($videoReport as $key => $value) {
            $videoReport[$key]['time'] = date('Y-m-d H:i', $value['time']);

            if (empty($videoReport[$key + 1])) continue;
            $videoReport[$key]['view_trend'] = $value['view'] - $videoReport[$key + 1]['view'];
            $videoReport[$key]['danmaku_trend'] = $value['danmaku'] - $videoReport[$key + 1]['danmaku'];
            $videoReport[$key]['reply_trend'] = $value['reply'] - $videoReport[$key + 1]['reply'];
            $videoReport[$key]['favorite_trend'] = $value['favorite'] - $videoReport[$key + 1]['favorite'];
            $videoReport[$key]['coin_trend'] = $value['coin'] - $videoReport[$key + 1]['coin'];
            $videoReport[$key]['likes_trend'] = $value['likes'] - $videoReport[$key + 1]['likes'];
            $videoReport[$key]['dislike_trend'] = $value['dislike'] - $videoReport[$key + 1]['dislike'];
        }

        return $videoReport;
    }
}
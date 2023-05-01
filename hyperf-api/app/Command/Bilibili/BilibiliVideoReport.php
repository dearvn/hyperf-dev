<?php
namespace App\Command\Bilibili;

use App\Foundation\Facades\Log;
use App\Model\Laboratory\Bilibili\Video;
use App\Model\Laboratory\Bilibili\VideoReport;
use App\Service\Laboratory\Bilibili\VideoService;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Di\Annotation\Inject;

/**
 * @Crontab(name="BilibiliVideoReport", rule="00 * * * *", callback="execute", memo="BilibiliUpVideo data report collection")
 */
class BilibiliVideoReport
{
    /**
     *
     * @Inject()
     * @var Video
     */
    private $video;

    /**
     * Timed recording video data changes
     * @throws \Exception
     */
    public function execute()
    {
        try {
            //Get the UP owner who needs to get the data statement regularly
            $videoBVidList = $this->video->newQuery()
                ->where('timed_status', Video::TIMED_STATUS_ON)
                ->pluck('bvid')->toArray();

            foreach ($videoBVidList as $bvid) {
                //Get anchor data
                $videoReport = VideoService::getInstance()->getVideoInfoFromBilibili($bvid);

                if (!empty($videoReport)) {
                    //Write data report
                    $insertData['time'] = strtotime(date('Y-m-d H:i'));
                    $insertData['bvid'] = $bvid;
                    $insertData['mid'] = $videoReport['mid'];
                    $insertData['view'] = $videoReport['view'] ?? 0;
                    $insertData['danmaku'] = $videoReport['danmaku'] ?? 0;
                    $insertData['reply'] = $videoReport['reply'] ?? 0;
                    $insertData['favorite'] = $videoReport['favorite'] ?? 0;
                    $insertData['coin'] = $videoReport['coin'] ?? 0;
                    $insertData['likes'] = $videoReport['likes'] ?? 0;
                    $insertData['dislike'] = $videoReport['dislike'] ?? 0;
                    VideoReport::query()->insert($insertData);

                    //Modify anchor information to the latest data
                    $updateData['view'] = $videoReport['view'] ?? 0;
                    $updateData['danmaku'] = $videoReport['danmaku'] ?? 0;
                    $updateData['reply'] = $videoReport['reply'] ?? 0;
                    $updateData['favorite'] = $videoReport['favorite'] ?? 0;
                    $updateData['coin'] = $videoReport['coin'] ?? 0;
                    $updateData['likes'] = $videoReport['likes'] ?? 0;
                    $updateData['dislike'] = $videoReport['dislike'] ?? 0;
                    $updateData['owner'] = !empty($videoReport['owner']) ? json_encode($videoReport['owner']) : '';
                    $updateData['updated_at'] = date('Y-m-d H:i:s');
                    Video::where('bvid', $bvid)->update($updateData);
                }
            }
        }catch (\Exception $e) {
            Log::crontabLog()->error($e->getMessage());
            //If an error is reported, re -execute
            $obj = new BilibiliVideoReport();
            $obj->execute();
        }
    }
}

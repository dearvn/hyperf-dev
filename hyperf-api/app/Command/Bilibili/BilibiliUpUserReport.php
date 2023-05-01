<?php
namespace App\Command\Bilibili;

use App\Exception\Handler\BusinessException;
use App\Foundation\Facades\Log;
use App\Model\Laboratory\Bilibili\UpUser;
use App\Model\Laboratory\Bilibili\UpUserReport;
use App\Service\Laboratory\Bilibili\UpUserService;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Di\Annotation\Inject;

/**
 * @Crontab(name="BilibiliUpUserReport", rule="00 * * * *", callback="execute", memo="BilibiliUpMain data collection timing task")
 */
class BilibiliUpUserReport
{
    /**
     *
     * @Inject()
     * @var UpUser
     */
    private $biliUpUser;

    /**
     * Record the changes in the data of the anchor data
     * @throws \Exception
     */
    public function execute()
    {
        try {
            //Get the UP owner who needs to get the data statement regularly
            $upUserMidList = $this->biliUpUser->newQuery()
                ->where('timed_status', UpUser::TIMED_STATUS_ON)
                ->pluck('mid')->toArray();

            foreach ($upUserMidList as $upUserMid) {
                //Get anchor data
                $upUserReport = UpUserService::getInstance()->getUpUserInfoFromBilibili($upUserMid);

                if (!empty($upUserReport)) {
                    //Write data report
                    $insertData['time'] = strtotime(date('Y-m-d H:i'));
                    $insertData['mid'] = $upUserMid;
                    $insertData['following'] = $upUserReport['following'] ?? 0;
                    $insertData['follower'] = $upUserReport['follower'] ?? 0;
                    $insertData['video_play'] = $upUserReport['video_play'] ?? 0;
                    $insertData['readling'] = $upUserReport['readling'] ?? 0;
                    $insertData['likes'] = $upUserReport['likes'] ?? 0;
                    $insertData['recharge_month'] = $upUserReport['recharge_month'] ?? 0;
                    $insertData['recharge_total'] = $upUserReport['recharge_total'] ?? 0;
                    UpUserReport::query()->insert($insertData);

                    //Modify anchor information to the latest data
                    $updateData['name'] = $upUserReport['name'] ?? '';
                    $updateData['sex'] = $upUserReport['sex'] ?? '';
                    $updateData['sign'] = $upUserReport['sign'] ?? '';
                    $updateData['face'] = $upUserReport['face'] ?? '';
                    $updateData['level'] = $upUserReport['level'] ?? '';
                    $updateData['top_photo'] = $upUserReport['top_photo'] ?? '';
                    $updateData['birthday'] = $upUserReport['birthday'] ?? '';
                    $updateData['following'] = $upUserReport['following'] ?? 0;
                    $updateData['follower'] = $upUserReport['follower'] ?? 0;
                    $updateData['video_play'] = $upUserReport['video_play'] ?? 0;
                    $updateData['readling'] = $upUserReport['readling'] ?? 0;
                    $updateData['likes'] = $upUserReport['likes'] ?? 0;
                    $updateData['recharge_month'] = $upUserReport['recharge_month'] ?? 0;
                    $updateData['recharge_total'] = $upUserReport['recharge_total'] ?? 0;
                    $updateData['live_room_info'] = $upUserReport['live_room_info'] ?? '';
                    $updateData['updated_at'] = date('Y-m-d H:i:s');
                    UpUser::where('mid', $upUserMid)->update($updateData);
                }
            }
        }catch (\Exception $e) {
           Log::crontabLog()->error($e->getMessage());
           //If an error is reported, re -execute
           $obj = new BilibiliUpUserReport();
           $obj->execute();
        }
    }
}

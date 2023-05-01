<?php
declare(strict_types=1);

namespace App\Process;

use App\Constants\Laboratory\ChatRedisKey;
use App\Pool\Redis;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Process\AbstractProcess;
use Hyperf\Process\Annotation\Process;
use Hyperf\WebSocketServer\Sender;

/**
 * Link to monitor WS
 * Class MonitorWsProcess
 * @Process(name="monitor_ws")
 * @package App\Process
 * @Author YiYuan-Lin
 * @Date: 2021/3/13
 */
class MonitorWsProcess extends AbstractProcess
{
    /**
     * @Inject()
     * @var Sender
     */
    private $sender;

    /**
     * Surveillance WS user connection situation
     */
    public function handle(): void
    {
        while (true) {
            $onlineUserList = Redis::getInstance()->hGetAll(ChatRedisKey::ONLINE_USER_FD_KEY);
            foreach ($onlineUserList as $key => $val) {
                if (!$this->sender->check(intval($val))) {
                    Redis::getInstance()->hDel(ChatRedisKey::ONLINE_USER_FD_KEY, (string) $key);
                    Redis::getInstance()->hDel(ChatRedisKey::ONLINE_FD_USER_KEY, (string) $val);
                }
            }
            sleep(1);
        }
    }
}
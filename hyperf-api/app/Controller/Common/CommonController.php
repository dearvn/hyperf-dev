<?php

declare(strict_types=1);

namespace App\Controller\Common;

use App\Controller\AbstractController;
use App\Model\System\GlobalConfig;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;

/**
 * Public request controller
 * @Controller(prefix="common")
 */
class CommonController extends AbstractController
{
    /**
     * Get the system configuration
     * @RequestMapping(path="sys_config", methods="get")
     * @return  \Psr\Http\Message\ResponseInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getSysConfig()
    {
        $configList = GlobalConfig::query()->select('key_name', 'data')
            ->where('type', GlobalConfig::TYPE_BY_BOOLEAN)
            ->get()->toArray();

        $result = [];
        foreach ($configList as $item) {
            $result[$item['key_name']] = boolval($item['data']);
        }
        return $this->success($result);
    }
}

<?php
namespace App\Service\System;

use App\Foundation\Traits\Singleton;
use App\Foundation\Utils\FreeApi;
use App\Service\BaseService;

/**
 * Login log service
 * Class LoginLogService
 * @package App\Service\System
 * @Author YiYuan-Lin
 * @Date: 2020/12/16
 */
class LoginLogService extends BaseService
{
    use Singleton;

    /**
     * Collect the operation log information
     * @return array
     */
    public function collectLoginLogInfo() : array
    {
        //Get the request parameter
        $requireParams = $this->request->all();

        //Get login information
        $loginIp = getClientIp($this->request) ?? '';
        $ipAddress = FreeApi::getResult($loginIp);
        $province = empty($ipAddress['province']) ? '' : $ipAddress['province'];
        $city = empty($ipAddress['city']) ? '' : $ipAddress['city'];
        $loginAddress = $province . $city;
        $browser = get_browser_os();
        $os = get_os();
        $loginTime = date('Y-m-d H:i:s');

        return [
           'username' => $requireParams['username'] ?? '',
           'login_ip' => $loginIp,
           'login_address' => $loginAddress,
           'login_browser' => $browser,
           'os' => $os,
           'login_date' => $loginTime,
        ];
    }

}

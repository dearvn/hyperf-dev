<?php
namespace App\Service\Auth;

use App\Constants\StatusCode;
use App\Foundation\Traits\Singleton;
use App\Service\BaseService;
use App\Model\Auth\User;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Context;
use Phper666\JWTAuth\JWT;

/**
 * User service basic class
 * Class UserService
 * @package App\Service\Auth
 * @Author YiYuan-Lin
 * @Date: 2020/10/29
 */
class UserService extends BaseService
{
    use Singleton;

    /**
     * @Inject()
     * @var JWT
     */
    private $jwt;

    /**
     * Obtain user information based on token
     * @return object
     */
    public function getUserInfoByToken() : object
    {
        //Data obtained token analysis
        $parserData = $this->jwt->getParserData();
        $userId = $parserData['uid'];

        $userInfo = User::getOneByUid($userId);
        return $userInfo;
    }
}

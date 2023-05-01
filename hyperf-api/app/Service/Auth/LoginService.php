<?php
namespace App\Service\Auth;

use App\Constants\StatusCode;
use App\Foundation\Traits\Singleton;
use App\Service\BaseService;
use App\Service\System\LoginLogService;
use App\Model\Auth\Permission;
use App\Model\Auth\User;
use App\Model\System\LoginLog;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\ApplicationContext;
use Phper666\JWTAuth\JWT;

/**
 * Basic Class Login Service
 * Class LoginService
 * @package App\Service\Auth
 * @Author YiYuan-Lin
 * @Date: 2020/10/29
 */
class LoginService extends BaseService
{
    use Singleton;

    /**
     * @Inject()
     * @var JWT
     */
    private $jwt;

    /**
     * Process login logic
     * @param array $params
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function login(array $params) : array
    {
        //Get user information
        $user = User::query()->where('username', $params['username'])->first();

        //Check whether the user and the password are correct and check whether the account is suspended
        if (empty($user)) $this->throwExp(StatusCode::ERR_USER_ABSENT,'Log in failed, users do not exist');
        if (md5($params['password']) != $user->password) $this->throwExp(StatusCode::ERR_USER_PASSWORD,'Log in failure, user verification fails, password errors');
        if ($user['status'] != 1)  $this->throwExp(StatusCode::ERR_USER_DISABLE,'This account has been suspended, please contact the administrator');

        //If the verification code is the test environment, the verification code verification
        if (!env('APP_TEST')) {
            $container = ApplicationContext::getContainer();
            $redis = $container->get(\Hyperf\Redis\Redis::class);
            $code = $redis->get($params['code_key']);
            
            if (strtolower($params['captcha']) != strtolower($code)) $this->throwExp(StatusCode::ERR_CODE, 'verification code error'.$code);
        }
        $userData = [
            'uid' => $user->id, //If you log in with a single point, there must be the value of the sso_key in the configuration file. Generally, it is set to the user’s ID
            'username' => $user->username,
        ];
        $token = $this->jwt->getToken($userData);

        //Update user information
        $user->last_login = time();
        $user->last_ip = getClientIp($this->request);
        $user->save();
        $responseData = $this->respondWithToken($token);

        //Record login log
        $loginLogData = LoginLogService::getInstance()->collectLoginLogInfo();
        $loginLogData['response_code'] = 200;
        $loginLogData['response_result'] = 'Landed successfully';
        LoginLog::add($loginLogData);

        return $responseData;
    }

    /**
     * Processing registration logic
     * @param array $params
     * @return array
     */
    public function register(array $params) : bool
    {
        //If the verification code is the test environment, the verification code verification
        if (!env('APP_TEST')) {
            $container = ApplicationContext::getContainer();
            $redis = $container->get(\Hyperf\Redis\Redis::class);
            $code = $redis->get($params['code_key']);
            if (strtolower($params['captcha']) != strtolower($code)) $this->throwExp(StatusCode::ERR_CODE, 'Verification failure, verification code error');
        }
        $postData = $this->request->all();

        $user = new User();
        $user->username = $postData['username'];
        $user->password = md5($postData['password']);
        $user->status = User::STATUS_ON;
        $user->avatar = 'https://shmily-album.oss-cn-shenzhen.aliyuncs.com/admin_face/face' . rand(1,10) .'.png';
        $user->last_login = time();
        $user->last_ip = getClientIp($this->request);
        $user->creater = 'none';
        $user->desc = $postData['desc'] ?? '';
        $user->sex = User::SEX_BY_Female;

        if (!$user->save()) $this->throwExp(StatusCode::ERR_EXCEPTION, 'Registered users failed');
        $user->assignRole('tourist_admin');
        return true;
    }

    /**
     * Log in to initialize, obtain user information and some permissions menus
     * @return mixed
     */
    public function initialization() : array
    {
        $responseData = [];
        //Get user information
        $user = UserService::getInstance()->getUserInfoByToken();
        $userInfo = objToArray($user);
        unset($userInfo['roles']);
        unset($userInfo['permissions']);

        $menu = $this->getMenuList($user);
        $responseData['user_info'] = objToArray($userInfo);
        $responseData['role_info'] = $user->getRoleNames();
        $responseData['menu_header'] = $menu['menuHeader'];
        $responseData['menu_list'] = $menu['menuList'];
        $responseData['permission'] = $menu['permission'];
        $responseData['permission_info'] = $menu['permission_info'];

        return $responseData;
    }

    /**
     * The processing permissions get the routing (provided to the front -end registration routing)
     * @return array
     */
    public function getRouters() :array
    {
        $userInfo = conGet('user_info');
        $permissionList = Permission::getUserPermissions($userInfo);
        $permissionList = objToArray($permissionList);
        $permissionList = array_column($permissionList, null, 'id');

        foreach ($permissionList as $key => $val) {
            if ($val['status'] == Permission::OFF_STATUS) unset($permissionList[$key]);
            if ($val['type'] == Permission::BUTTON_OR_API_TYPE) unset($permissionList[$key]);
        }

        //Use the reference to transmit the recursive array
        $routers = [
           'default' => [
                'path' => '',
                'component' => 'Layout',
                'redirect' => '/home',
                'children' => [],
            ]
        ];
        $module_children = [];
        foreach($permissionList as $key => $value){
            if(isset($permissionList[$value['parent_id']])){
                $permissionList[$value['parent_id']]['children'][] = &$permissionList[$key];
            }else{
                $module_children[] = &$permissionList[$key];
            }
        }
        foreach ($module_children as $key => $value) {
            if (!empty($value['children'])) {
                $routers[$value['id']] = [
                    'name' => $value['name'],
                    'path' => $value['url'],
                    'redirect' => 'noRedirect',
                    'hidden' => $value['hidden'],
                    'alwaysShow' => true,
                    'component' => $value['component'],
                    'meta' => [
                        'icon' => $value['icon'],
                        'title' => $value['display_name'],
                    ],
                    'children' => []
                ];
                $routers[$value['id']]['children'] = $this->dealRouteChildren($value['children']);
            }else {
                array_push($routers['default']['children'], [
                    'name' => $value['name'],
                    'path' => $value['url'],
                    'hidden' => $value['hidden'],
                    'alwaysShow' => true,
                    'component' => $value['component'],
                    'meta' => [
                        'icon' => $value['icon'],
                        'title' => $value['display_name'],
                    ],
                ]);
            }
        }
        return array_values($routers);
    }

    /**
     * treatTheLowerTopRoutingOfTheRoute
     * @param array $children
     * @return array
     */
    private function dealRouteChildren(array $children) : array
    {
        $temp = [];
        if (!empty($children)) {
            foreach ($children as $k => $v) {
                if ($v['type'] == Permission::MENU_TYPE) {
                    $temp[] = [
                        'name' => $v['name'],
                        'path' => $v['url'],
                        'hidden' => $v['hidden'],
                        'alwaysShow' => true,
                        'component' => $v['component'],
                        'meta' => [
                            'icon' => $v['icon'],
                            'title' => $v['display_name'],
                        ],
                    ];
                }
                if (!empty($v['children'])) {
                    $temp = array_merge($temp, $this->dealRouteChildren($v['children']));
                }
            }
        }
        return $temp;
    }

    /**
     * Process token data
     * @param $token
     * @return array
     */
    protected function respondWithToken(string $token) : array
    {
        $data = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' =>  $this->jwt->getTTL(),
        ];
        return $data;
    }

    /**
     * Get head menu data and menu list
     * @param object $user
     * @return array
     */
    protected function getMenuList(object $user) : array
    {
        //Get the menu tree shape
        $menuList = Permission::getUserMenuList($user);
        $permission = Permission::getUserPermissions($user);
        $menuHeader = [];
        foreach ($menuList as $key => $val) {
            if ($val['status'] != 0) {
                $menuHeader[] = [
                    'title' => $val['display_name'],
                    'icon' => $val['icon'],
                    'path' => $val['url'],
                    'name' => $val['name'],
                    'id' => $val['id'],
                    'type' => $val['type'],
                    'sort' => $val['sort'],
                ];
            }
        }
        //Sort
        array_multisort(array_column($menuHeader, 'sort'), SORT_ASC, $menuHeader);

        return [
            'menuList' => $menuList,
            'menuHeader' => $menuHeader,
            'permission' => array_column($permission, 'name'),
            'permission_info' => $permission,
        ];
    }


}

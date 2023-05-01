<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Constants\StatusCode;
use App\Constants\UploadCode;
use App\Controller\AbstractController;
use App\Foundation\Annotation\Explanation;
use App\Model\Laboratory\FriendRelation;
use App\Service\Auth\UserService;
use App\Model\Auth\User;
use Donjan\Permission\Models\Role;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use App\Middleware\RequestMiddleware;
use App\Middleware\PermissionMiddleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;
use League\Flysystem\Filesystem;

/**
 * User controller
 * Class UserController
 * @Controller(prefix="setting/user_module/user")
 */
class UserController extends AbstractController
{
    /**
     * @Inject()
     * @var User
     */
    private $user;

    /**
     * @Inject()
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Get user data list
     * @RequestMapping(path="list", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function index()
    {
        $userQuery = $this->user->newQuery();

        if (!empty($this->request->input('role_name'))) {
            $role_id = Role::query()->where('name', $this->request->input('role_name'))->value('id');
            if (!empty($role_id)) {
                $userQuery->from('users as a');
                $userQuery->leftJoin('model_has_roles as b', 'a.id', 'b.model_id');
                $userQuery->where('b.role_id', $role_id);
            }
        }
        $status = $this->request->input('status') ?? '';
        if (!empty($this->request->input('username'))) $userQuery->where('username', 'like', '%' . $this->request->input('username') . '%');
        if (!empty($this->request->input('desc'))) $userQuery->where('desc', 'like', '%' . $this->request->input('desc') . '%');
        if (strlen($status)) $userQuery->where('status', $status);
        $total = $userQuery->count();
        $userQuery = $this->pagingCondition($userQuery, $this->request->all());
        $data = $userQuery->get();

        foreach ($data as $key => $value) {
            $data[$key]['roleData'] = $value->getRoleNames();
        }

        return $this->success([
            'list' => $data,
            'total' => $total,
        ]);
    }

    /**
     * @Explanation(content="Add user operation")
     * @RequestMapping(path="store", methods="post")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function store()
    {
        $postData = $this->request->all();
        $params = [
            'username' => $postData['username'] ?? '',
            'password' => $postData['password'] ?? '',
            'password_confirmation' => $postData['password_confirmation'] ?? '',
            'status' => $postData['status'] ?? 1,
            'mobile' => $postData['mobile'] ?? '',
            'roleData' => $postData['roleData'] ?? '',
        ];
        //Configuration verification
        $rules = [
            'username' => 'required|min:4|max:18|unique:users',
            'password' => 'required|min:6|max:18|confirmed:password_confirmation',
            'password_confirmation' => 'required',
            'status' => 'required',
            'mobile' => 'required',
            'roleData' => 'required|array',
        ];
        //Error message
        $message = [
            'username.required' => '[username] required',
            'username.unique' => 'This user name already exists',
            'password.required' => '[password] required',
            'confirm_password.required' => '[confirm_password] required',
            'roleData.required' => '[roleData] required',
            'roleData.array' => '[roleData] must be array',
            'username.min' => '[username] at least 4 digits',
            'username.max' => '[username] up to 18 digits',
            'password.min' => '[password] at least 6 digits',
            'password.max' => '[password] up to 18 digits',
            'password.confirmed' => 'Two password inputs are inconsistent',
            'mobile.required' => 'Phone number can not be blank',
        ];
        $this->verifyParams($params, $rules, $message);
        Db::beginTransaction();

        $user = new User();
        $user->username = $postData['username'];
        $user->password = md5($postData['password']);
        $user->status = $postData['status'] ?? '1';
        $user->avatar = empty($postData['avatar']) ? 'https://shmily-album.oss-cn-shenzhen.aliyuncs.com/admin_face/face' . rand(1,10) .'.png' : $postData['avatar'];
        $user->last_login = time();
        $user->last_ip = getClientIp($this->request);
        $user->creater = $postData['creater'] ?? 'none';
        $user->desc = $postData['desc'] ?? '';
        $user->mobile = $postData['mobile'] ?? '';
        $user->email = $postData['email'] ?? '';
        $user->address = $postData['address'] ?? '';
        $user->sex = $postData['sex'] ?? 0;
        if (!$user->save()) $this->throwExp(StatusCode::ERR_EXCEPTION, 'Add user failure');

        //Assign role permissions
        foreach ($postData['roleData'] as $key) {
            $user->assignRole($key);
        }
        DB::commit();

        return $this->successByMessage('Add user success');
    }

    /**
     * Get the data of a single user
     * @param int $id
     * @RequestMapping(path="edit/{id}", methods="get")
     * @Middleware(RequestMiddleware::class)
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function edit(int $id)
    {
        $userInfo = User::getOneByUid($id);
        if (empty($userInfo)) $this->throwExp(StatusCode::ERR_USER_ABSENT, 'Failure to obtain user information');
        $userInfo['roleData'] = $userInfo->getRoleNames();
        unset($userInfo['roles']);

        return $this->success([
            'list' => $userInfo
        ]);
    }


    /**
     * Get the data of the current login users
     * @RequestMapping(path="profile", methods="get")
     * @Middleware(RequestMiddleware::class)
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function profile()
    {
        $userInfo = UserService::getInstance()->getUserInfoByToken();
        $roleArr = '';
        foreach ($userInfo->roles as $key => $value) {
            $roleArr .= $value['description'] . ' ';
        }
        $userInfo->last_login = date('Y-m-d H:i:s', $userInfo->last_login);

        return $this->success([
            'list' => $userInfo,
            'role' => $roleArr
        ]);
    }

    /**
     * Get the data of the current login users
     * @param int $id
     * @RequestMapping(path="profile/{id}", methods="put")
     * @Middleware(RequestMiddleware::class)
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function profileEdit($id)
    {
        if (empty($id)) $this->throwExp(StatusCode::ERR_VALIDATION, 'ID Can not be empty');
        $postData = $this->request->all();

        $user = User::getOneByUid($id);
        $user->email = $postData['email'] ?? '';
        $user->desc = $postData['desc'] ?? '';
        $user->mobile = $postData['mobile'] ?? '';
        $user->sex = $postData['sex'] ?? '';
        $user->address = $postData['address'] ?? '';
        if (!$user->save()) $this->throwExp(StatusCode::ERR_EXCEPTION,  'Modify user information failure');

        //Return to the information correctly
        return $this->successByMessage('Modify the success of the user');
    }

    /**
     * @Explanation(content="Upload user avatar")
     * @RequestMapping(path="upload_avatar", methods="post")
     * @Middleware(RequestMiddleware::class)
     */
    public function uploadAvatar()
    {
        $params = [
            'savePath' => $this->request->input('save_path'),
            'file' => $this->request->file('file'),
            'id' => $this->request->input('id'),
        ];
        //Configuration verification
        $rules = [
            'id' => 'required',
            'savePath' => 'required',
            'file' => 'required |file',
        ];
        $message = [
            'id.required' => '[id] required',
            'savePath.required' => '[savePath] required',
            'file.required' => '[name] required',
            'file.file' => '[file] The parameter must be the file type',
        ];
        $this->verifyParams($params, $rules, $message);

        if ($params['file']->getSize() > 30000000) $this->throwExp(UploadCode::ERR_UPLOAD_SIZE, 'Upload the picture size is too large');

        //Stringing the file name and the corresponding path
        $fileName =  md5(uniqid())  . '.' . 'jpg';
        $uploadPath = $params['savePath'] . '/' . $fileName;

        //The path of external network visits
        $fileUrl = env('OSS_URL') . $uploadPath;

        $stream = fopen($params['file']->getRealPath(), 'r+');
        $this->filesystem->writeStream(
            $uploadPath,
            $stream
        );
        if (is_resource($stream)) {
            fclose($stream);
        }
        $user = User::getOneByUid($params['id']);
        $user->avatar = $fileUrl;
        $user->save();
        if (!$user->save()) $this->throwExp(StatusCode::ERR_VALIDATION, 'Modify the user\'s avatar failure');

        return $this->success([
            'fileName' => $fileName,
            'url' => $fileUrl
        ], 'Upload the picture successfully');
    }


    /**
     * @Explanation(content="Modify user information")
     * @param int $id
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     * @RequestMapping(path="update/{id}", methods="put")
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function update(int $id)
    {
        if (empty($id)) $this->throwExp(StatusCode::ERR_VALIDATION, 'ID can not be empty');
        $postData = $this->request->all();

        $params = [
            'status' => $postData['status'] ?? 1,
            'mobile' => $postData['mobile'] ?? '',
            'roleData' => $postData['roleData'] ?? '',
        ];
        //Configuration verification
        $rules = [
            'status'    => 'required',
            'mobile'    => 'required',
            'roleData'  => 'required|array',
        ];
        //Error message
        $message = [
            'roleData.required' => '[roleData] required',
            'roleData.array' => '[roleData] must be array',
            'username.min' => '[username] at least 4 digits',
            'username.max' => '[username] up to 18 digits',
            'password.confirmed' => 'Two password inputs are inconsistent',
            'mobile.required' => 'Phone number can not be blank',
        ];

        // form validation
        $this->verifyParams($params, $rules, $message);

        //Starting affairs
        DB::beginTransaction();

        $user = User::getOneByUid($id);
        $user->status = $postData['status'] ?? '1';
        $user->avatar = $postData['avatar'] ?? 'https://shmily-album.oss-cn-shenzhen.aliyuncs.com/admin_face/face' . rand(1,10) .'.png';
        $user->desc = $postData['desc'] ?? '';
        $user->mobile = $postData['mobile'] ?? '';
        $user->sex = $postData['sex'] ?? '';
        $user->address = $postData['address'] ?? '';
        $user->email = $postData['email'] ?? '';
        $user->status = $postData['status'] ?? '';
        if (!$user->save()) $this->throwExp(StatusCode::ERR_EXCEPTION,  'Modify user information failure');

        //Remove all characters and re -give the role
        DB::table('model_has_roles')
            ->where('model_id', $id)
            ->delete();
        foreach ($params['roleData'] as $key => $val) {
            $user->assignRole($val);
        }
        //Submit
        DB::commit();

        //Return to the information correctly
        return $this->successByMessage('Modify the success of the user');
    }

    /**
     * @Explanation(content="delete users")
     * @param int $id
     * @RequestMapping(path="destroy/{id}", methods="delete")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function destroy(int $id)
    {
        if ($id == 0) {
            $idArr = $this->request->input('id') ?? [];
            if (empty($idArr) || !is_array($idArr)) $this->throwExp(StatusCode::ERR_VALIDATION, 'The parameter type is incorrect');
            if (!User::whereIn('id', $idArr)->delete()) $this->throwExp(StatusCode::ERR_EXCEPTION, 'failed to delete');

            //Clean up chat with friends
            FriendRelation::query()->whereIn('uid', $idArr)->orWhereIn('friend_id', $idArr)->delete();
        }else {
            if (!intval($id)) $this->throwExp(StatusCode::ERR_VALIDATION, 'Parameter error');
            if (!User::destroy($id)) $this->throwExp(StatusCode::ERR_EXCEPTION, 'failed to delete');
            FriendRelation::query()->where('uid', $id)->orWhere('friend_id', $id)->delete();
        }
        return $this->successByMessage('Delete user success');
    }

    /**
     * @Explanation(content="Modify the user password")
     * @RequestMapping(path="reset_password", methods="post")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function resetPassword()
    {
        $postData = $this->request->all()['postData'] ?? [];
        $params = [
            'id' => $postData['uid'],
            'old_password' => $postData['old_password'] ?? '',
            'new_password' => $postData['new_password'] ?? '',
            'confirm_password' => $postData['confirm_password'] ?? '',
        ];
        //Configuration verification
        $rules = [
            'id' => 'required',
            'new_password' => 'required|min:6|max:18',
            'confirm_password' => 'required',
        ];
        $message = [
            'id.required' => '[id] required',
            'new_password.required' => '[new_password] required',
            'new_password.min' => '[new_password]最少6位',
            'new_password.max' => '[new_password]最多18位',
            'confirm_password.required' => '[confirm_password] required',
        ];

        $this->verifyParams($params, $rules, $message);
        $userInfo = User::getOneByUid($params['id']);

        if (empty($userInfo)) $this->throwExp(400, 'Account does not exist');
        if (md5($params['new_password']) != md5($params['confirm_password'])) $this->throwExp(StatusCode::ERR_EXCEPTION, 'Two password inputs are inconsistent');
        if (!empty($params['old_password']) && md5($params['old_password']) != $userInfo['password']) $this->throwExp(StatusCode::ERR_EXCEPTION, 'Old password verification failed, please try it out');

        $userInfo->password  = md5($params['new_password']);
        $updateRes = $userInfo->save();

        if (!$updateRes) $this->throwExp(400, 'Modify the password failure');
        return $this->success([], 'password has been updated');
    }

    /**
     * @Explanation(content="Modify the user status")
     * @RequestMapping(path="change_status", methods="post")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function changeStatus()
    {
        $postData = $this->request->all() ?? [];
        $params = [
            'id' => $postData['id'],
            'status' => $postData['status']
        ];
        //Configuration verification
        $rules = [
            'id' => 'required',
            'status' => 'required',
        ];
        $message = [
            'id.required' => '[id] required',
            'status.required' => '[status] required',
        ];

        $this->verifyParams($params, $rules, $message);
        $userInfo = User::getOneByUid($params['id']);

        if (empty($userInfo)) $this->throwExp(400, 'Account does not exist');

        $userInfo->status = $params['status'];
        $updateRes = $userInfo->save();

        if (!$updateRes) $this->throwExp(400, 'Change the user status and fail');

        return $this->success([], 'Change the user status successfully');
    }
}

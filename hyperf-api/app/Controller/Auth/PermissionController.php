<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Constants\StatusCode;
use App\Controller\AbstractController;
use App\Foundation\Annotation\Explanation;
use App\Model\Auth\Permission;
use App\Model\Auth\Role;
use App\Model\Auth\User;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use App\Middleware\RequestMiddleware;
use App\Middleware\PermissionMiddleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;

/**
 * Permissions controller
 * Class PermissionController
 * @Controller(prefix="setting/user_module/permission")
 */
class PermissionController extends AbstractController
{
    /**
     * @Inject()
     * @var Permission
     */
    private $permission;

    /**
     * List of obtaining permissions data
     * @RequestMapping(path="list", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function index()
    {
        $permissionQuery = $this->permission->newQuery();
        if (!empty($this->request->input('display_name'))) $permissionQuery->where('display_name', 'like', '%' . $this->request->input('display_name') .'%');
        if (!empty($this->request->input('name'))) $permissionQuery->where('name', 'like', '%' . $this->request->input('name') .'%');
        if (strlen($this->request->input('status') ?? '') > 0) $permissionQuery->where('status', $this->request->input('status'));

        $permissionList = $permissionQuery->get()->toArray();
        $permissionList = array_column($permissionList, null, 'id');

        //Use the reference to transmit the recursive array
        $list = [];
        foreach($permissionList as $key => $value){
            if(isset($permissionList[$value['parent_id']])){
                $permissionList[$value['parent_id']]['children'][] = &$permissionList[$key];
            }else{
                $list[] = &$permissionList[$key];
            }
        }

        return $this->success([
            'list' => $list,
        ]);
    }

    /**
     * Based on user acquisition permissions (for allocation of user permissions)
     * @RequestMapping(path="tree_by_user", methods="get")
     * @Middleware(RequestMiddleware::class)
     */
    public function treeByUser()
    {
        $userId = $this->request->all()['user_id'] ?? '';
        if (empty($userId)) $this->throwExp(StatusCode::ERR_VALIDATION, 'User ID required');

        //Get user information
        $userInfo = User::getOneByUid($userId);

        //Get all the functional permissions enabled by the system
        $permissionList = Permission::getAllPermissionByTree();

        //Obtain the permissions owned by users
        $userHasPermission = Permission::getUserPermissions($userInfo);
        $userHasPermission = array_column($userHasPermission, 'name');

        return $this->success([
            'permission_list' => $permissionList,
            'user_has_permission' => $userHasPermission
        ]);
    }

    /**
     * Based on the role of the character acquisition permissions (for allocation of the role permissions)
     * @RequestMapping(path="tree_by_role", methods="get")
     * @Middleware(RequestMiddleware::class)
     */
    public function treeByRole()
    {
        $roleId = $this->request->all()['role_id'] ?? '';
        if (empty($roleId)) $this->throwExp(StatusCode::ERR_VALIDATION, 'Lost character ID');

        //Get user information
        $roleInfo = Role::getOneByRoleId($roleId);

        //Get all the functional permissions enabled by the system
        $permissionList = Permission::getAllPermissionByTree();

        //Get the permissions of the character
        $roleHasPermission = $roleInfo->permissions->toArray();
        $roleHasPermission = array_column($roleHasPermission, 'name');

        return $this->success([
            'permission_list' => $permissionList,
            'role_has_permission' => $roleHasPermission
        ]);
    }

    /**
     * @Explanation(content="Add permissions operation")
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
            'parent_id' => $postData['parent_id'] ?? 0,
            'name' => $postData['name'] ?? '',
            'display_name' => $postData['display_name'] ?? '',
            'type' => $postData['type'] ?? ''
        ];
        //Configuration verification
        $rules = [
            'parent_id' => 'required',
            'name' => 'required',
            'display_name' => 'required',
            'type' => 'required',
        ];
        $message = [
            'parent_id.required' => '[parent_id]required',
            'name.required' => '[name]required',
            'type.required' => '[type]required',
            'display_name.required' => '[display_name]required',
        ];
        $this->verifyParams($params, $rules, $message);
        $permission = new Permission();
        $permission->parent_id = $params['parent_id'];
        $permission->type = $params['type'];
        $permission->name = $params['name'];
        $permission->display_name = $params['display_name'];
        $permission->display_desc = $postData['display_desc'] ?? '';
        $permission->url = $postData['url'] ?? '';
        $permission->component = $postData['component'] ?? '';
        $permission->guard_name = $postData['guard_name'] ?? '';
        $permission->icon = $postData['icon'] ?? '';
        $permission->hidden = $postData['hidden'] ?? false;
        $permission->status = $postData['status'] ?? 1;
        $permission->sort = $postData['sort'] ?? 99;
        $permission->guard_name = 'web';
        $permission->created_at = date('Y-m-d H:i:s');
        $permission->updated_at = date('Y-m-d H:i:s');

        if (!$permission->save()) $this->throwExp(StatusCode::ERR_VALIDATION, 'Adding permissions failed');
        //Clear cache operation
        $permission->forgetCachedPermissions();

        return $this->successByMessage('Add permission successfully');
    }

    /**
     * Get data from single permissionsfrom single permissions
     * @param int $id
     * @RequestMapping(path="edit/{id}", methods="get")
     * @Middleware(RequestMiddleware::class)
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function edit(int $id)
    {

        $permissionInfo = Permission::findById($id);
        if (empty($permissionInfo)) $this->throwExp(StatusCode::ERR_VALIDATION, 'Obtaining permissions information failure');

        return $this->success([
            'list' => $permissionInfo
        ]);
    }

    /**
     * @Explanation(content="Modify permissions operation")
     * @param int $id
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function update(int $id)
    {
        $postData = $this->request->all();
        $params = [
            'id' => $id,
            'parent_id' => $postData['parent_id'] ?? 0,
            'name' => $postData['name'] ?? '',
            'display_name' => $postData['display_name'] ?? '',
            'type' => $postData['type'] ?? ''
        ];
        //Configuration verification
        $rules = [
            'id' => 'required',
            'parent_id' => 'required',
            'name' => 'required',
            'display_name' => 'required',
            'type' => 'required',
        ];
        $message = [
            'id.required' => '[id]required',
            'parent_id.required' => '[parent_id]required',
            'name.required' => '[name]required',
            'type.required' => '[type]required',
            'display_name.required' => '[display_name]required',
        ];

        $this->verifyParams($params, $rules, $message);
        $permission = Permission::find($id);
        $permission->parent_id = $params['parent_id'];
        $permission->type = $params['type'];
        $permission->name = $params['name'];
        $permission->display_name = $params['display_name'];
        $permission->display_desc = $postData['display_desc'] ?? '';
        $permission->url = $postData['url'] ?? '';
        $permission->component = $postData['component'] ?? '';
        $permission->guard_name = $postData['guard_name'] ?? '';
        $permission->icon = $postData['icon'] ?? '';
        $permission->hidden = $postData['hidden'] ?? false;
        $permission->status = $postData['status'] ?? 1;
        $permission->sort = $postData['sort'] ?? 99;
        $permission->updated_at = date('Y-m-d H:i:s');
        if (!$permission->save()) $this->throwExp(StatusCode::ERR_VALIDATION, 'Modify the authority information failed');

        $permission->forgetCachedPermissions();
        return $this->successByMessage('Modify permissions information successfully');
    }

    /**
     * @Explanation(content="Delete permissions operation")
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
        $params = [
            'id' => $id,
        ];
        //Configuration verification
        $rules = [
            'id' => 'required',
        ];
        $message = [
            'id.required' => 'Illegal parameter',
        ];

        $this->verifyParams($params, $rules, $message);

        if (!Permission::query()->where('parent_id', $id)->get()->isEmpty()) $this->throwExp(StatusCode::ERR_VALIDATION, 'There is also a child permissions under this permissions, and the deletion fails');
            
        if (!Permission::query()->where('id', $id)->delete()) $this->throwExp(StatusCode::ERR_VALIDATION, 'Delete permissions information failure');
        return $this->successByMessage('Delete permissions information successfully');
    }

    /**
     * @Explanation(content="Assign user characters")
     * @RequestMapping(path="accord_user_role", methods="post")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function accordUserRole()
    {
        $postData = $this->request->all() ?? [];
        $params = [
            'user_id' => $postData['user_id'],
            'role_list' => $postData['role_list'],
        ];
        //Configuration verification
        $rules = [
            'user_id' => 'required',
            'role_list' => 'required|array',
        ];
        $message = [
            'user_id.required' => '[user_id]required',
            'role_list.required' => 'Please choose at least one role',
            'role_list.array' => 'The character data format is incorrect',
        ];
        $this->verifyParams($params, $rules, $message);

        $userModel = User::getOneByUid($params['user_id']);

        //Clear all the characters of the current user first
        Db::table('model_has_roles')
            ->where('user_id', $params['user_id'])
            ->delete();

        if (!$userModel->syncRoles($params['role_list'])) $this->throwExp(StatusCode::ERR_EXCEPTION, 'Failure to allocate user characters');
        return $this->successByMessage( '分配用户角色成功');
    }


    /**
     * @Explanation(content="Assign role permissions")
     * @RequestMapping(path="accord_role_permission", methods="post")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function accordRolePermission()
    {
        $postData = $this->request->all() ?? [];
        $params = [
            'role_id' => $postData['role_id'],
            'role_has_permission' => $postData['role_has_permission'],
        ];
        //Configuration verification
        $rules = [
            'role_id' => 'required|int',
            'role_has_permission' => 'required|array',
        ];
        $message = [
            'role_id.required' => '[role_id] required',
            'role_id.int' => '[role_id] the parameter format is incorrect',
            'role_has_permission.required' => 'Please select at least one permissions',
            'role_has_permission.array' => 'Permanent data format incorrect',
        ];
        $this->verifyParams($params, $rules, $message);

        $roleModel = Role::findById(intval($params['role_id']));

        //First clear the current role ownership permissions
        Db::table('role_has_permissions')
            ->where('role_id', $params['role_id'])
            ->delete();

        if (!$roleModel->syncPermissions($params['role_has_permission'])) $this->throwExp(StatusCode::ERR_EXCEPTION, 'Failure to allocate role permissions');
        return $this->successByMessage( 'Successful assignment role permissions');
    }

    /**
     * @Explanation(content="Allocate user permissions")
     * @RequestMapping(path="accord_user_permission", methods="post")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function accordUserPermission()
    {
            $postData = $this->request->all() ?? '';
            $params = [
                'user_id' => $postData['user_id'] ?? '',
                'user_has_permission' => $postData['user_has_permission'] ?? ''
            ];
            $rules = [
                'user_id' => 'required|int',
                'user_has_permission' => 'required|array',
            ];
            $message = [
                'user_id.required' => '[user_id]required',
                'user_id.int' => '[user_id] parameter type error',
                'user_has_permission.required' => 'Please select at least one permissions',
                'user_has_permission.array' => 'Permanent data format incorrect',
            ];
            $this->verifyParams($params, $rules, $message);

            //According to user information
            $userModel = User::query()->where('id', $params['user_id'])->first();

            //Get the role of the user and the correspondence permissions
            $roleName = $userModel->getRoleNames();
            $roleList = Role::query()->whereIn('name', $roleName)->get();
            $roleHasPermission = [];
            foreach ($roleList as $role)  {
                $roleHasPermission = array_merge($roleHasPermission, array_column($role->permissions->toArray(), 'name'));
            }
            $roleHasPermission = array_values(array_unique($roleHasPermission));
            $userHasPermission = array_diff($params['user_has_permission'], $roleHasPermission);

            //First clear the current user ownership permissions
            DB::table('model_has_permissions')
                ->where('model_id', $params['user_id'])
                ->delete();
            //Allocate user permissions
            if (!$userModel->syncPermissions($userHasPermission)) $this->throwExp(StatusCode::ERR_EXCEPTION, 'Failure to allocate user permissions');
            return $this->successByMessage('Successful allocation of user authority');
    }
}

<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Constants\StatusCode;
use App\Controller\AbstractController;
use App\Foundation\Annotation\Explanation;
use App\Model\Auth\Role;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use App\Middleware\RequestMiddleware;
use App\Middleware\PermissionMiddleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;

/**
 * Role controller
 * Class RoleController
 * @Controller(prefix="setting/user_module/role")
 */
class RoleController extends AbstractController
{
    /**
     * @Inject()
     * @var Role
     */
    private $role;

    /**
     * Get the role data list
     * @RequestMapping(path="list", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function index()
    {
        $roleQuery = $this->role->newQuery();

        $total = $roleQuery->count();
        $roleQuery = $this->pagingCondition($roleQuery, $this->request->all());
        //Determine whether there are query conditions
        if (!empty($this->request->input('description'))) $roleQuery->where('description', 'like', '%' . $this->request->input('description') . '%');
        $list = $roleQuery->get();

        return $this->success([
            'list' => $list,
            'total' => $total,
        ]);
    }

    /**
     * Get the role data list
     * @RequestMapping(path="tree", methods="get")
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function tree()
    {
        $roleQuery = $this->role->newQuery();

        $list = $roleQuery->select('name', 'description')->get()->toArray();

        return $this->success([
            'list' => $list,
        ]);
    }

    /**
     * @Explanation(content="Add role operation")
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
            'name' => $postData['name'] ?? '',
            'description' => $postData['description'] ?? '',
        ];
        //Configuration verification
        $rules = [
            'name' => 'required',
        ];
        $message = [
            'name.required' => '[name] required',
        ];
        $this->verifyParams($params, $rules, $message);

        if (!Role::create($params)) $this->throwExp(400, 'Add role failure');

        return $this->successByMessage('Successful role');
    }

    /**
     * Get the data of a single role
     * @param int $id
     * @RequestMapping(path="edit/{id}", methods="get")
     * @Middleware(RequestMiddleware::class)
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function edit(int $id)
    {
        $roleInfo = Role::getOneByRoleId($id);
        if (empty($roleInfo)) $this->throwExp(StatusCode::ERR_VALIDATION, 'Failure to get role information');

        return $this->success([
            'list' => $roleInfo
        ]);
    }

    /**
     * @Explanation(content="Modify the role operation")
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
            'name' => $postData['name'],
            'description' => $postData['description']
        ];
        //Configuration verification
        $rules = [
            'id' => 'required',
            'name' => 'required',
        ];
        $message = [
            'id.required' => 'Parameter is invalid',
            'name.required' => '[name] required',
        ];

        $this->verifyParams($params, $rules, $message);

        if (!Role::query()->where('id', $id)->update($params)) $this->throwExp(400, 'Modify character information failed');

        return $this->successByMessage('Modify character information successfully');
    }

    /**
     * @Explanation(content="Delete character operation")
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
            'id.required' => 'Parameter is invalid',
        ];

        $this->verifyParams($params, $rules, $message);

        if (!Role::query()->where('id', $id)->delete()) $this->throwExp(400, 'Delete character information failure');

        return $this->successByMessage('Delete character information successfully');
    }

}

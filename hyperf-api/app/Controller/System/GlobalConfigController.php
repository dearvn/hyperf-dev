<?php

declare(strict_types=1);

namespace App\Controller\System;

use App\Constants\StatusCode;
use App\Controller\AbstractController;
use App\Foundation\Annotation\Explanation;
use App\Model\System\GlobalConfig;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Middleware\RequestMiddleware;
use App\Middleware\PermissionMiddleware;

/**
 * Global parameter configuration
 * Class GlobalConfigController
 * @package App\Controller\System
 * @Controller(prefix="setting/system_module/global_config")
 * @Author YiYuan-Lin
 * @Date: 2021/6/10
 */
class GlobalConfigController extends AbstractController
{
    /**
     * @Inject()
     * @var GlobalConfig
     */
    private $globalConfig;

    /**
     * 获取参数列表
     * @RequestMapping(path="list", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function index()
    {
        $globalConfigQuery = $this->globalConfig->newQuery();
        $name = $this->request->input('name') ?? '';
        $keyName = $this->request->input('key_name') ?? '';
        $type = $this->request->input('type') ?? '';

        if (!empty($name)) $globalConfigQuery->where('name', 'like', '%'. $name . '%');
        if (!empty($keyName)) $globalConfigQuery->where('key_name', 'like', '%'. $keyName . '%');
        if (!empty($type)) $globalConfigQuery->where('type', $type);

        $total = $globalConfigQuery->count();
        $globalConfigQuery->orderBy('created_at', 'desc');
        $globalConfigQuery = $this->pagingCondition($globalConfigQuery, $this->request->all());
        $data = $globalConfigQuery->get();

        return $this->success([
            'list' => $data,
            'total' => $total,
        ]);
    }

    /**
     * @Explanation(content="Add global parameters")
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
            'key_name' => $postData['key_name'] ?? '',
            'data' => $postData['data'] ?? '',
            'remark' => $postData['remark'] ?? '',
            'type' => $postData['type'] ?? '',
        ];
        //Configuration verification
        $rules = [
            'name' => 'required',
            'key_name' => 'required',
            'type' => 'required',
        ];
        //Error message
        $message = [
            'name.required' => '[name] required',
            'key_name.required' => '[key_name] required',
            'type.required' => '[type] required',
        ];
        $this->verifyParams($params, $rules, $message);

        $globalConfigQuery = new GlobalConfig();
        $globalConfigQuery->name = $params['name'];
        $globalConfigQuery->key_name = $params['key_name'];
        $globalConfigQuery->data = $params['data'];
        $globalConfigQuery->remark = $params['remark'];
        $globalConfigQuery->type = $params['type'];

        if (!$globalConfigQuery->save()) $this->throwExp(StatusCode::ERR_EXCEPTION, 'Add global parameter error');

        return $this->successByMessage('Add global parameter success');
    }

    /**
     * Get a single global parameter information
     * @param int $id
     * @RequestMapping(path="edit/{id}", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function edit(int $id)
    {
        $globalConfigInfo = GlobalConfig::findById($id);
        if (empty($globalConfigInfo)) $this->throwExp(StatusCode::ERR_USER_ABSENT, 'Failure to obtain global parameters');

        if ($globalConfigInfo['type'] == GlobalConfig::TYPE_BY_BOOLEAN) {
            $globalConfigInfo['data'] = boolval($globalConfigInfo['data']);
        }

        return $this->success([
            'list' => $globalConfigInfo
        ]);
    }

    /**
     * @Explanation(content="Modify global parameter information")
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
        if (empty($id)) $this->throwExp(StatusCode::ERR_VALIDATION, 'ID can not be empty');
        $postData = $this->request->all();
        $params = [
            'name' => $postData['name'] ?? '',
            'key_name' => $postData['key_name'] ?? '',
            'data' => $postData['data'] ?? '',
            'remark' => $postData['remark'] ?? '',
            'type' => $postData['type'] ?? '',
        ];
        //Configuration verification
        $rules = [
            'name' => 'required',
            'key_name' => 'required',
            'type' => 'required',
        ];
        //Error message
        $message = [
            'name.required' => '[name] required',
            'key_name.required' => '[key_name] required',
            'type.required' => '[type] required',
        ];
        $this->verifyParams($params, $rules, $message);

        $globalConfigQuery = GlobalConfig::findById($id);
        $globalConfigQuery->name = $params['name'];
        $globalConfigQuery->key_name = $params['key_name'];
        $globalConfigQuery->data = $params['data'];
        $globalConfigQuery->remark = $params['remark'];
        $globalConfigQuery->type = $params['type'];

        if (!$globalConfigQuery->save()) $this->throwExp(StatusCode::ERR_EXCEPTION, 'Modify global parameter errors');

        return $this->successByMessage('Modify global parameters success');
    }

    /**
     * @Explanation(content="Delete global parameters")
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
            if (!GlobalConfig::whereIn('id', $idArr)->delete()) $this->throwExp(StatusCode::ERR_EXCEPTION, 'failed to delete');
        }else {
            if (!intval($id)) $this->throwExp(StatusCode::ERR_VALIDATION, 'Parameter error');
            if (!GlobalConfig::destroy($id)) $this->throwExp(StatusCode::ERR_EXCEPTION, 'failed to delete');
        }

        return $this->successByMessage('Delete global parameters successfully');
    }

}
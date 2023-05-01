<?php

declare(strict_types=1);

namespace App\Controller\System;

use App\Constants\StatusCode;
use App\Controller\AbstractController;
use App\Foundation\Annotation\Explanation;
use App\Model\System\DictData;
use App\Model\System\DictType;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Middleware\RequestMiddleware;
use App\Middleware\PermissionMiddleware;

/**
 * Dictionary type controller
 * Class IndexController
 * @Controller(prefix="setting/system_module/dict_type")
 */
class DictTypeController extends AbstractController
{
    /**
     * @Inject()
     * @var DictType
     */
    private $dictType;

    /**
     * List of dictionary types
     * @RequestMapping(path="list", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function index()
    {
        $dictTypeQuery = $this->dictType->newQuery();
        $status = $this->request->input('status') ?? '';
        $dictName = $this->request->input('dict_name') ?? '';
        $dictType = $this->request->input('dict_type') ?? '';

        if (!empty($dictName)) $dictTypeQuery->where('dict_name', 'like', '%' . $dictName . '%');
        if (!empty($dictType)) $dictTypeQuery->where('dict_type', 'like', '%' . $dictType . '%');
        if (strlen($status) > 0) $dictTypeQuery->where('status', $status);

        $total = $dictTypeQuery->count();
        $dictTypeQuery = $this->pagingCondition($dictTypeQuery, $this->request->all());
        $data = $dictTypeQuery->get();

        return $this->success([
            'list' => $data,
            'total' => $total,
        ]);
    }

    /**
     * @Explanation(content="Add dictionary type")
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
            'dict_name' => $postData['dict_name'] ?? '',
            'dict_type' => $postData['dict_type'] ?? '',
            'status' => $postData['status'] ?? 1,
            'remark' => $postData['remark'] ?? '',
        ];
        //Configuration verification
        $rules = [
            'dict_name' => 'required|min:2|max:60|',
            'dict_type' => 'required|unique:dict_type',
        ];
        //Error message
        $message = [
            'dict_name.required' => '[dict_name] required',
            'dict_name.min' => '[dict_name] at least 2 digits',
            'dict_name.max' => '[dict_name] up to 60 digits',
            'dict_type.required' => '[dict_type] required',
            'dict_type.unique' => '[dict_type] already exists',
        ];
        $this->verifyParams($params, $rules, $message);

        $dictTypeQuery = new DictType();
        $dictTypeQuery->dict_name = $params['dict_name'];
        $dictTypeQuery->dict_type = $params['dict_type'];
        $dictTypeQuery->status = $params['status'];
        $dictTypeQuery->remark = $params['remark'];
        $dictTypeQuery->created_at = date('Y-m-d, H:i:s');
        $dictTypeQuery->updated_at = date('Y-m-d, H:i:s');
        if (!$dictTypeQuery->save()) $this->throwExp(StatusCode::ERR_EXCEPTION, 'Add dictionary type error');

        return $this->successByMessage('Added dictionary type successful');
    }

    /**
     * Get a single dictionary type information
     * @param int $id
     * @RequestMapping(path="edit/{id}", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function edit(int $id)
    {
        $dictTypeInfo = DictType::findById($id);
        if (empty($dictTypeInfo)) $this->throwExp(StatusCode::ERR_USER_ABSENT, 'Failure to obtain dictionary information');

        return $this->success([
            'list' => $dictTypeInfo
        ]);
    }

    /**
     * @Explanation(content="Modify the dictionary type information")
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
            'dict_name' => $postData['dict_name'] ?? '',
            'dict_type' => $postData['dict_type'] ?? '',
            'status' => $postData['status'] ?? 1,
            'remark' => $postData['remark'] ?? '',
        ];
        $rules = [
            'dict_name' => 'required|min:4|max:18|',
            'dict_type' => 'required',
        ];
        $message = [
            'dict_name.required' => '[dict_name] required',
            'dict_name.min' => '[dict_name]最少4位',
            'dict_name.max' => '[dict_name]最多18位',
            'dict_type.required' => '[dict_type] required',
        ];
        $this->verifyParams($params, $rules, $message);

        $dictTypeQuery = DictType::findById($id);
        $dictTypeQuery->dict_name = $params['dict_name'];
        $dictTypeQuery->dict_type = $params['dict_type'];
        $dictTypeQuery->status = $params['status'];
        $dictTypeQuery->remark = $params['remark'];
        $dictTypeQuery->updated_at = date('Y-m-d, H:i:s');
        if (!$dictTypeQuery->save()) $this->throwExp(StatusCode::ERR_EXCEPTION, 'Modify the dictionary type error');

        return $this->successByMessage('Modify the dictionary type success');
    }

    /**
     * @Explanation(content="Delete dictionary type")
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
            if (!DictType::whereIn('dict_id', $idArr)->delete()) $this->throwExp(StatusCode::ERR_EXCEPTION, 'failed to delete');
        }else {
            if (!intval($id)) $this->throwExp(StatusCode::ERR_VALIDATION, 'Parameter error');
            if (!DictType::destroy($id)) $this->throwExp(StatusCode::ERR_EXCEPTION, 'failed to delete');
        }

        return $this->successByMessage('Delete the timing task successfully');
    }

}
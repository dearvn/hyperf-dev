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
 * Dictionary data controller
 * Class DictDataController
 * @Controller(prefix="setting/system_module/dict_data")
 */
class DictDataController extends AbstractController
{
    /**
     * @Inject()
     * @var DictData
     */
    private $dictData;

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
        $dictDataQuery = $this->dictData->newQuery();
        $status = $this->request->input('status');
        $dictLabel = $this->request->input('dict_label') ?? '';
        $dictType = $this->request->input('dict_type') ?? '';

        if (!empty($dictLabel)) $dictDataQuery->where('dict_label', 'like', '%' . $dictLabel . '%');
        if (!empty($dictType)) $dictDataQuery->where('dict_type', 'like', '%' . $dictType . '%');
        if (strlen($status) > 0) $dictDataQuery->where('status', $status);

        $total = $dictDataQuery->count();
        $dictDataQuery = $this->pagingCondition($dictDataQuery, $this->request->all());
        $data = $dictDataQuery->get();

        return $this->success([
            'list' => $data,
            'total' => $total,
        ]);
    }
    /**
     * Obtain dictionary data based on the dictionary type
     * @param string $dictType
     * @RequestMapping(path="dict/{dictType}", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getDict(string $dictType)
    {
        if (!is_string($dictType) && empty($dictType)) $this->throwExp(StatusCode::ERR_VALIDATION, 'The dictionary type is empty or the parameter format is incorrect');

        $list = DictData::query()->where('dict_type', $dictType)->get()->toArray();
        foreach ($list as $key => $val) {
            if(is_numeric($val['dict_value'])) $list[$key]['dict_value'] = intval($val['dict_value']);
        }
        return $this->success([
            'list' => $list,
        ]);
    }

    /**
     * @Explanation(content="Add dictionary data")
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
            'dict_type' => $postData['dict_type'] ?? '',
            'dict_label' => $postData['dict_label'] ?? '',
            'dict_value' => $postData['dict_value'] ?? '',
            'dict_sort' => $postData['dict_sort'] ?? 1,
            'status' => $postData['status'] ?? 1,
            'remark' => $postData['remark'] ?? '',
        ];
        //Configuration verification
        $rules = [
            'dict_type' => 'required',
            'dict_label' => 'required',
            'dict_value' => 'required',
        ];
        //Error message
        $message = [
            'dict_type.required' => '[dict_type] required',
            'dict_label.required' => '[dict_label] required',
            'dict_value.required' => '[dict_value] required',
        ];
        $this->verifyParams($params, $rules, $message);

        $dictDataQuery = new DictData();
        $dictDataQuery->dict_type = $params['dict_type'];
        $dictDataQuery->dict_label = $params['dict_label'];
        $dictDataQuery->dict_value = $params['dict_value'];
        $dictDataQuery->dict_sort = $params['dict_sort'];
        $dictDataQuery->status = $params['status'];
        $dictDataQuery->remark = $params['remark'];
        $dictDataQuery->created_at = date('Y-m-d, H:i:s');
        $dictDataQuery->updated_at = date('Y-m-d, H:i:s');
        if (!$dictDataQuery->save()) $this->throwExp(StatusCode::ERR_EXCEPTION, 'Add dictionary data error');

        return $this->successByMessage('Add dictionary data data successfully');
    }

    /**
     * Get a single dictionary data information
     * @param int $id
     * @RequestMapping(path="edit/{id}", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function edit(int $id)
    {
        $dictDataInfo = DictData::findById($id);
        if (empty($dictDataInfo)) $this->throwExp(StatusCode::ERR_USER_ABSENT, 'Failure to obtain dictionary information');

        return $this->success([
            'list' => $dictDataInfo
        ]);
    }

    /**
     * @Explanation(content="Modify the dictionary data information")
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
        if (empty($id)) $this->throwExp(StatusCode::ERR_VALIDATION, 'ID Can not be empty');
        $postData = $this->request->all();
        $params = [
            'dict_type' => $postData['dict_type'] ?? '',
            'dict_label' => $postData['dict_label'] ?? '',
            'dict_value' => $postData['dict_value'] ?? '',
            'dict_sort' => $postData['dict_sort'] ?? 1,
            'status' => $postData['status'] ?? 1,
            'remark' => $postData['remark'] ?? '',
        ];
        //Configuration verification
        $rules = [
            'dict_type' => 'required',
            'dict_label' => 'required',
            'dict_value' => 'required',
        ];
        //Error message
        $message = [
            'dict_type.required' => '[dict_type] required',
            'dict_label.required' => '[dict_label] required',
            'dict_value.required' => '[dict_value] required',
        ];
        $this->verifyParams($params, $rules, $message);

        $dictDataQuery = DictData::findById($id);
        $dictDataQuery->dict_type = $params['dict_type'];
        $dictDataQuery->dict_label = $params['dict_label'];
        $dictDataQuery->dict_value = $params['dict_value'];
        $dictDataQuery->dict_sort = $params['dict_sort'];
        $dictDataQuery->status = $params['status'];
        $dictDataQuery->remark = $params['remark'];
        $dictDataQuery->updated_at = date('Y-m-d, H:i:s');
        if (!$dictDataQuery->save()) $this->throwExp(StatusCode::ERR_EXCEPTION, 'Modify the dictionary data error');

        return $this->successByMessage('Modify the dictionary data successfully');
    }

    /**
     * @Explanation(content="Delete dictionary data")
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
            if (!DictData::whereIn('dict_code', $idArr)->delete()) $this->throwExp(StatusCode::ERR_EXCEPTION, 'failed to delete');
        }else {
            if (!intval($id)) $this->throwExp(StatusCode::ERR_VALIDATION, 'Parameter error');
            if (!DictData::destroy($id)) $this->throwExp(StatusCode::ERR_EXCEPTION, 'failed to delete');
        }

        return $this->successByMessage('Delete the timing task successfully');
    }

}
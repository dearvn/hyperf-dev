<?php

declare(strict_types=1);

namespace App\Controller\System;

use App\Constants\StatusCode;
use App\Controller\AbstractController;
use App\Foundation\Annotation\Explanation;
use App\Model\System\Advice;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Middleware\RequestMiddleware;
use App\Middleware\PermissionMiddleware;

/**
 * System suggestion controller
 * Class IndexController
 * @Controller(prefix="setting/system_module/advice")
 */
class AdviceController extends AbstractController
{
    /**
     * @Inject()
     * @var Advice
     */
    private $advice;

    /**
     * Get the system suggestion list
     * @RequestMapping(path="list", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function index()
    {
        $adviceQuery = $this->advice->newQuery();
        $status = $this->request->input('status') ?? '';
        $type = $this->request->input('type') ?? '';

        if (strlen($status) > 0) $adviceQuery->where('status', $status);
        if (strlen($type) > 0) $adviceQuery->where('type', $type);

        $total = $adviceQuery->count();
        $adviceQuery->with('getUserName:id,desc');
        $adviceQuery->orderBy('created_at', 'desc');
        $adviceQuery = $this->pagingCondition($adviceQuery, $this->request->all());
        $data = $adviceQuery->get();

        return $this->success([
            'list' => $data,
            'total' => $total,
        ]);
    }

    /**
     * @Explanation(content="Add system suggestion")
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
            'title' => $postData['title'] ?? '',
            'type' => $postData['type'] ?? '',
            'content' => $postData['content'] ?? '',
        ];
        //Configuration verification
        $rules = [
            'title' => 'required',
            'type' => 'required|integer',
            'content' => 'required',
        ];
        //Error message
        $message = [
            'title.required' => '[title] required',
            'type.required' => '[type] required',
            'type.integer' => '[type] incorrect type',
            'content.required' => '[content] required',
        ];
        $this->verifyParams($params, $rules, $message);

        $adviceQuery = new Advice();
        $adviceQuery->title = $params['title'];
        $adviceQuery->type = $params['type'];
        $adviceQuery->content = $params['content'];
        $adviceQuery->reply = '';
        $adviceQuery->status = 0;
        $adviceQuery->user_id = conGet('user_info')['id'];

        if (!$adviceQuery->save()) $this->throwExp(StatusCode::ERR_EXCEPTION, 'Add system suggestion errors');

        return $this->successByMessage('Add system suggestion success');
    }

    /**
     * Get a single system recommendation information
     * @param int $id
     * @RequestMapping(path="edit/{id}", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function edit(int $id)
    {
        $adviceInfo = Advice::findById($id);
        if (empty($adviceInfo)) $this->throwExp(StatusCode::ERR_USER_ABSENT, 'Failure to obtain dictionary information');

        return $this->success([
            'list' => $adviceInfo
        ]);
    }

    /**
     * @Explanation(content="Modify the system recommendation information")
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
            'title' => $postData['title'] ?? '',
            'type' => $postData['type'] ?? '',
            'content' => $postData['content'] ?? '',
        ];
        //Configuration verification
        $rules = [
            'title' => 'required',
            'type' => 'required|integer',
            'content' => 'required',
        ];
        //Error message
        $message = [
            'title.required' => '[title] required',
            'type.required' => '[type] required',
            'type.integer' => '[type] incorrect type',
            'content.required' => '[content] required',
        ];
        $this->verifyParams($params, $rules, $message);

        $adviceQuery = Advice::findById($id);
        $adviceQuery->title = $params['title'];
        $adviceQuery->type = $params['type'];
        $adviceQuery->content = $params['content'];

        if (!$adviceQuery->save()) $this->throwExp(StatusCode::ERR_EXCEPTION, 'Modify the system suggestion errors');

        return $this->successByMessage('Modify the system for success');
    }

    /**
     * @Explanation(content="Reply suggestion")
     * @param int $id
     * @RequestMapping(path="reply/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function reply(int $id)
    {
        if (empty($id)) $this->throwExp(StatusCode::ERR_VALIDATION, 'ID can not be empty');
        $postData = $this->request->all();
        $params = [
            'reply' => $postData['reply'] ?? '',
            'status' => $postData['status'] ?? '',
        ];
        //Configuration verification
        $rules = [
            'reply' => 'required',
            'status' => 'required|integer',
        ];
        //Error message
        $message = [
            'reply.required' => '[reply] required',
            'status.required' => '[status] required',
            'status.integer' => '[status] incorrect type',
        ];
        $this->verifyParams($params, $rules, $message);

        $adviceQuery = Advice::findById($id);
        $adviceQuery->reply = $params['reply'];
        $adviceQuery->status = $params['status'];

        if (!$adviceQuery->save()) $this->throwExp(StatusCode::ERR_EXCEPTION, 'Reply to the system\'s recommended error');

        return $this->successByMessage('Reply to the system\'s recommended success');
    }

    /**
     * @Explanation(content="Delete system suggestions")
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
            if (!Advice::whereIn('id', $idArr)->delete()) $this->throwExp(StatusCode::ERR_EXCEPTION, 'failed to delete');
        }else {
            if (!intval($id)) $this->throwExp(StatusCode::ERR_VALIDATION, 'Parameter error');
            if (!Advice::destroy($id)) $this->throwExp(StatusCode::ERR_EXCEPTION, 'failed to delete');
        }

        return $this->successByMessage('Delete system Suggestions');
    }

}
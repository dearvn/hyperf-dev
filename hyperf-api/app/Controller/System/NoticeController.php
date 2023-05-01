<?php

declare(strict_types=1);

namespace App\Controller\System;

use App\Constants\StatusCode;
use App\Controller\AbstractController;
use App\Foundation\Annotation\Explanation;
use App\Foundation\Utils\Queue;
use App\Job\EmailNotificationJob;
use App\Model\System\Notice;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Middleware\RequestMiddleware;
use App\Middleware\PermissionMiddleware;

/**
 * System notification controller
 * Class NoticeController
 * @package App\Controller\System
 * @Controller(prefix="setting/system_module/notice")
 * @Author YiYuan-Lin
 * @Date: 2021/3/3
 */
class NoticeController extends AbstractController
{
    /**
     * @Inject()
     * @var Notice
*/
    private $notice;

    /**
     * @Inject()
     * @var Queue
     */
    private $queue;

    /**
     * Get the system notification list
     * @RequestMapping(path="list", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function index()
    {
        $noticeQuery = $this->notice->newQuery();
        $status = $this->request->input('status') ?? '';
        $title = $this->request->input('title') ?? '';

        if (strlen($status) > 0) $noticeQuery->where('status', $status);
        if (!empty($title)) $noticeQuery->where('title', 'like', '%'. $title . '%');

        $total = $noticeQuery->count();
        $noticeQuery->with('getUserName:id,desc');
        $noticeQuery->orderBy('created_at', 'desc');
        $noticeQuery = $this->pagingCondition($noticeQuery, $this->request->all());
        $data = $noticeQuery->get();

        return $this->success([
            'list' => $data,
            'total' => $total,
        ]);
    }

    /**
     * @Explanation(content="Add system notification")
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
            'content' => $postData['content'] ?? '',
            'status' => $postData['status'] ?? '',
            'public_time' => $postData['public_time'] ?? '',
        ];
        //Configuration verification
        $rules = [
            'title' => 'required',
            'status' => 'required|integer',
            'content' => 'required',
            'public_time' => 'required',
        ];
        //Error message
        $message = [
            'title.required' => '[title] required',
            'status.required' => '[status] required',
            'status.integer' => '[status]类型不正确',
            'content.required' => '[content] required',
            'public_time.required' => '[public_time] required',
        ];
        $this->verifyParams($params, $rules, $message);

        $noticeQuery = new Notice();
        $noticeQuery->title = $params['title'];
        $noticeQuery->status = $params['status'];
        $noticeQuery->content = $params['content'];
        $noticeQuery->public_time = strtotime($params['public_time']);
        $noticeQuery->user_id = conGet('user_info')['id'];
        $noticeQuery->username = conGet('user_info')['desc'];

        if (!$noticeQuery->save()) $this->throwExp(StatusCode::ERR_EXCEPTION, 'Add system notification error');

        //Distribution queue
        $this->queue->push(new EmailNotificationJob([
            'title' => $params['title'],
            'content' => $params['content'],
        ]));
        return $this->successByMessage('Add system notification success');
    }

    /**
     * Get a single system notification information
     * @param int $id
     * @RequestMapping(path="edit/{id}", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function edit(int $id)
    {
        $noticeInfo = Notice::findById($id);
        $noticeInfo->public_time = date('Y-m-d H:i:s', $noticeInfo->public_time);
        if (empty($noticeInfo)) $this->throwExp(StatusCode::ERR_USER_ABSENT, 'Failure to obtain dictionary information');

        return $this->success([
            'list' => $noticeInfo
        ]);
    }

    /**
     * @Explanation(content="Modify the system notification information")
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
            'content' => $postData['content'] ?? '',
            'status' => $postData['status'] ?? '',
            'public_time' => $postData['public_time'] ?? '',
        ];
        //Configuration verification
        $rules = [
            'title' => 'required',
            'status' => 'required|integer',
            'content' => 'required',
            'public_time' => 'required',
        ];
        //Error message
        $message = [
            'title.required' => '[title] required',
            'status.required' => '[status] required',
            'status.integer' => '[status]类型不正确',
            'content.required' => '[content] required',
            'public_time.required' => '[public_time] required',
        ];
        $this->verifyParams($params, $rules, $message);

        $noticeQuery = Notice::findById($id);
        $noticeQuery->title = $params['title'];
        $noticeQuery->status = $params['status'];
        $noticeQuery->content = $params['content'];
        $noticeQuery->public_time = strtotime($params['public_time']);

        if (!$noticeQuery->save()) $this->throwExp(StatusCode::ERR_EXCEPTION, 'Modify the system notification error');

        return $this->successByMessage('Modify the system notification success');
    }

    /**
     * @Explanation(content="Delete system notification")
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
            if (!Notice::whereIn('id', $idArr)->delete()) $this->throwExp(StatusCode::ERR_EXCEPTION, 'failed to delete');
        }else {
            if (!intval($id)) $this->throwExp(StatusCode::ERR_VALIDATION, 'Parameter error');
            if (!Notice::destroy($id)) $this->throwExp(StatusCode::ERR_EXCEPTION, 'failed to delete');
        }

        return $this->successByMessage('Delete system notification success');
    }

}
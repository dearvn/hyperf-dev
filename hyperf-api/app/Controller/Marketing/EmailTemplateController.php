<?php

declare(strict_types=1);

namespace App\Controller\Marketing;

use App\Constants\StatusCode;
use App\Controller\AbstractController;
use App\Foundation\Annotation\Explanation;
use App\Model\Marketing\EmailTemplate;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use App\Middleware\RequestMiddleware;
use App\Middleware\PermissionMiddleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;

/**
 * EmailTemplate controller
 * Class EmailTemplateController
 * @Controller(prefix="marketing/email_module/template")
 */
class EmailTemplateController extends AbstractController
{
    /**
     * @Inject()
     * @var EmailTemplate
     */
    private $template;

    /**
     * Get the template data list
     * @RequestMapping(path="list", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function index()
    {
        $templateQuery = $this->template->newQuery();

        $total = $templateQuery->count();
        $templateQuery = $this->pagingCondition($templateQuery, $this->request->all());
        //Determine whether there are query conditions
        if (!empty($this->request->input('name'))) $templateQuery->where('name', 'like', '%' . $this->request->input('name') . '%');
        $list = $templateQuery->get();

        return $this->success([
            'list' => $list,
            'total' => $total,
        ]);
    }

    /**
     * @Explanation(content="Add template operation")
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

        if (!EmailTemplate::create($params)) $this->throwExp(400, 'Add template failure');

        return $this->successByMessage('Successful template');
    }

    /**
     * Get the data of a single template
     * @param int $id
     * @RequestMapping(path="edit/{id}", methods="get")
     * @Middleware(RequestMiddleware::class)
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function edit(int $id)
    {
        $templateInfo = EmailTemplate::findById($id);
        if (empty($templateInfo)) $this->throwExp(StatusCode::ERR_VALIDATION, 'Failure to get template information');

        return $this->success([
            'list' => $templateInfo
        ]);
    }

    /**
     * @Explanation(content="Modify the template operation")
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
            'id.required' => 'Id is invalid',
            'name.required' => '[name] required',
        ];

        $this->verifyParams($params, $rules, $message);

        if (!EmailTemplate::query()->where('id', $id)->update($params)) $this->throwExp(400, 'Updating template failed');

        return $this->successByMessage('Email EmailTemplate is updated successfully');
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

        if (!EmailTemplate::query()->where('id', $id)->delete()) $this->throwExp(400, 'Delete email template failure');

        return $this->successByMessage('Email template is deleted  successfully');
    }

}

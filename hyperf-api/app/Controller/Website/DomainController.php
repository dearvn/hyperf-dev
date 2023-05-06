<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Constants\StatusCode;
use App\Controller\AbstractController;
use App\Foundation\Annotation\Explanation;
use App\Model\Website\Domain;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use App\Middleware\RequestMiddleware;
use App\Middleware\PermissionMiddleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;

/**
 * Domain controller
 * Class DomainController
 * @Controller(prefix="website/domain_module/domain")
 */
class DomainController extends AbstractController
{
    /**
     * @Inject()
     * @var Domain
     */
    private $domain;

    /**
     * Get the domain data list
     * @RequestMapping(path="list", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function index()
    {
        $domainQuery = $this->domain->newQuery();

        $total = $domainQuery->count();
        $domainQuery = $this->pagingCondition($domainQuery, $this->request->all());
        //Determine whether there are query conditions
        if (!empty($this->request->input('name'))) $domainQuery->where('name', 'like', '%' . $this->request->input('name') . '%');
        $list = $domainQuery->get();

        return $this->success([
            'list' => $list,
            'total' => $total,
        ]);
    }

    /**
     * @Explanation(content="Add domain operation")
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

        if (!Domain::create($params)) $this->throwExp(400, 'Add domain failure');

        return $this->successByMessage('Successful domain');
    }

    /**
     * Get the data of a single domain
     * @param int $id
     * @RequestMapping(path="edit/{id}", methods="get")
     * @Middleware(RequestMiddleware::class)
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function edit(int $id)
    {
        $domainInfo = Domain::findById($id);
        if (empty($domainInfo)) $this->throwExp(StatusCode::ERR_VALIDATION, 'Failure to get domain information');

        return $this->success([
            'list' => $domainInfo
        ]);
    }

    /**
     * @Explanation(content="Modify the domain operation")
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

        if (!Domain::query()->where('id', $id)->update($params)) $this->throwExp(400, 'Updating domain failed');

        return $this->successByMessage('Landing Page Domain is updated successfully');
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

        if (!Domain::query()->where('id', $id)->delete()) $this->throwExp(400, 'Delete landing page domain failure');

        return $this->successByMessage('Landing Page Domain is deleted  successfully');
    }

}

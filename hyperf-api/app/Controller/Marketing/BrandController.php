<?php

declare(strict_types=1);

namespace App\Controller\Marketing;

use App\Constants\StatusCode;
use App\Controller\AbstractController;
use App\Foundation\Annotation\Explanation;
use App\Model\Marketing\Brand;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use App\Middleware\RequestMiddleware;
use App\Middleware\PermissionMiddleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;

/**
 * Brand controller
 * Class BrandController
 * @Controller(prefix="marketing/brand_module/brand")
 */
class BrandController extends AbstractController
{
    /**
     * @Inject()
     * @var Brand
     */
    private $brand;

    /**
     * Get the brand list
     * @RequestMapping(path="list", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function index()
    {
        $brandQuery = $this->brand->newQuery();

        $total = $brandQuery->count();
        $brandQuery = $this->pagingCondition($brandQuery, $this->request->all());
        //Determine whether there are query conditions
        if (!empty($this->request->input('name'))) $brandQuery->where('name', 'like', '%' . $this->request->input('name') . '%');
        $list = $brandQuery->get();

        return $this->success([
            'list' => $list,
            'total' => $total,
        ]);
    }

    /**
     * @Explanation(content="Add brand operation")
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
            'brand_name' => $postData['brand_name'] ?? '',
            
            'description' => $postData['description'] ?? '',
        ];
        //Configuration verification
        $rules = [
            'brand_name' => 'required',
        ];
        $message = [
            'brand_name.required' => '[brand_name] required',
        ];
        $this->verifyParams($params, $rules, $message);

        if (!Brand::create($params)) $this->throwExp(400, 'Add brand failure');

        return $this->successByMessage('Successful brand');
    }

    /**
     * Get the data of a single brand
     * @param int $id
     * @RequestMapping(path="edit/{id}", methods="get")
     * @Middleware(RequestMiddleware::class)
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function edit(int $id)
    {
        $brandInfo = Brand::findById($id);
        if (empty($brandInfo)) $this->throwExp(StatusCode::ERR_VALIDATION, 'Failure to get brand information');

        return $this->success([
            'list' => $brandInfo
        ]);
    }

    /**
     * @Explanation(content="Modify the brand operation")
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
            'brand_name' => $postData['brand_name'],
            'description' => $postData['description']
        ];
        //Configuration verification
        $rules = [
            'id' => 'required',
            'brand_name' => 'required',
        ];
        $message = [
            'id.required' => 'Id is invalid',
            'brand_name.required' => '[brand_name] required',
        ];

        $this->verifyParams($params, $rules, $message);

        if (!Brand::query()->where('id', $id)->update($params)) $this->throwExp(400, 'Updating brand failed');

        return $this->successByMessage('Email Brand is updated successfully');
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

        if (!Brand::query()->where('id', $id)->delete()) $this->throwExp(400, 'Delete email brand failure');

        return $this->successByMessage('Email brand is deleted  successfully');
    }

}

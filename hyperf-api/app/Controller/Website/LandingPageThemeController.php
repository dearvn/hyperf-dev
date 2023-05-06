<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Constants\StatusCode;
use App\Controller\AbstractController;
use App\Foundation\Annotation\Explanation;
use App\Model\Website\LandingPageTheme;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use App\Middleware\RequestMiddleware;
use App\Middleware\PermissionMiddleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;

/**
 * LandingPageTheme controller
 * Class LandingPageThemeController
 * @Controller(prefix="website/landingpage_module/theme")
 */
class LandingPageThemeController extends AbstractController
{
    /**
     * @Inject()
     * @var LandingPageTheme
     */
    private $theme;

    /**
     * Get the theme data list
     * @RequestMapping(path="list", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function index()
    {
        $themeQuery = $this->theme->newQuery();

        $total = $themeQuery->count();
        $themeQuery = $this->pagingCondition($themeQuery, $this->request->all());
        //Determine whether there are query conditions
        if (!empty($this->request->input('name'))) $themeQuery->where('name', 'like', '%' . $this->request->input('name') . '%');
        $list = $themeQuery->get();

        return $this->success([
            'list' => $list,
            'total' => $total,
        ]);
    }

    /**
     * @Explanation(content="Add theme operation")
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

        if (!LandingPageTheme::create($params)) $this->throwExp(400, 'Add theme failure');

        return $this->successByMessage('Successful theme');
    }

    /**
     * Get the data of a single theme
     * @param int $id
     * @RequestMapping(path="edit/{id}", methods="get")
     * @Middleware(RequestMiddleware::class)
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function edit(int $id)
    {
        $themeInfo = LandingPageTheme::findById($id);
        if (empty($themeInfo)) $this->throwExp(StatusCode::ERR_VALIDATION, 'Failure to get theme information');

        return $this->success([
            'list' => $themeInfo
        ]);
    }

    /**
     * @Explanation(content="Modify the theme operation")
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

        if (!LandingPageTheme::query()->where('id', $id)->update($params)) $this->throwExp(400, 'Updating theme failed');

        return $this->successByMessage('Landing Page Theme is updated successfully');
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

        if (!LandingPageTheme::query()->where('id', $id)->delete()) $this->throwExp(400, 'Delete landing page theme failure');

        return $this->successByMessage('Landing Page Theme is deleted  successfully');
    }

}

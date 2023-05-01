<?php

declare(strict_types=1);

namespace App\Controller\Blog;

use App\Constants\StatusCode;
use App\Controller\AbstractController;
use App\Foundation\Annotation\Explanation;
use App\Model\Blog\Photo;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Middleware\RequestMiddleware;
use App\Middleware\PermissionMiddleware;

/**
 * Image controller
 * Class PhotoController
 * @Controller(prefix="blog/picture_module/photo")
 */
class PhotoController extends AbstractController
{
    /**
     * @Inject()
     * @var Photo
     */
    private $photo;

    /**
     * Get the picture list
     * @RequestMapping(path="list", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function index()
    {
        $photoQuery = $this->photo->newQuery();
        $photoAlbum = $this->request->input('photo_album') ?? '';
        if (!empty($photoAlbum)) $photoQuery->where('photo_album', $photoAlbum);

        $total = $photoQuery->count();
        $photoQuery = $this->pagingCondition($photoQuery, $this->request->all());
        $photoQuery->with('getPhotoAlbum:id,album_name');
        $data = $photoQuery->get();

        return $this->success([
            'list' => $data,
            'total' => $total,
        ]);
    }

    /**
     * @Explanation(content="add pictures")
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
            'photo_url'  => $postData['photo_url'] ?? '',
            'photo_album'  => $postData['photo_album'] ?? '',
        ];
        //Configuration verification
        $rules = [
            'photo_url'  => 'required|array',
            'photo_album'  => 'required',
        ];
        //Error message
        $message = [
            'photo_url.required' => '[photo_url] required',
            'photo_url.array' => '[photo_url] the type must be array',
            'photo_album.required' => '[photo_album] required',
        ];
        $this->verifyParams($params, $rules, $message);

        if (is_array($params['photo_url'])) {
            foreach ($params['photo_url'] as $key) {
                Photo::query()->insert([
                    'photo_url' => $key,
                    'photo_album' => $params['photo_album'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
        return $this->successByMessage('Successfully add photos');
    }

    /**
     * @Explanation(content="Delete picture information")
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
            if (!Photo::whereIn('id', $idArr)->delete()) $this->throwExp(StatusCode::ERR_EXCEPTION, 'failed to delete');
        }else {
            if (!intval($id)) $this->throwExp(StatusCode::ERR_VALIDATION, 'Parameter error');
            if (!Photo::destroy($id)) $this->throwExp(StatusCode::ERR_EXCEPTION, 'failed to delete');
        }

        return $this->successByMessage('Delete the picture successfully');
    }
}

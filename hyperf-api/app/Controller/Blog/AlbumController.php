<?php

declare(strict_types=1);

namespace App\Controller\Blog;

use App\Constants\StatusCode;
use App\Controller\AbstractController;
use App\Foundation\Annotation\Explanation;
use App\Model\Blog\PhotoAlbum;
use App\Model\System\DictData;
use App\Model\System\GlobalConfig;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Middleware\RequestMiddleware;
use App\Middleware\PermissionMiddleware;

/**
 * Album controller
 * Class AlbumController
 * @Controller(prefix="blog/picture_module/album")
 */
class AlbumController extends AbstractController
{
    /**
     * @Inject()
     * @var PhotoAlbum
     */
    private $photoAlbum;

    /**
     * Get album list
     * @RequestMapping(path="list", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function index()
    {
        $photoAlbumQuery = $this->photoAlbum->newQuery();
        $albumName = $this->request->input('album_name') ?? '';
        $albumStatus = $this->request->input('album_status') ?? '';
        if (!empty($albumName)) $photoAlbumQuery->where('album_name', 'like', '%' . $albumName . '%');
        if (strlen($albumStatus) > 0) $photoAlbumQuery->where('album_status', $albumStatus);

        $total = $photoAlbumQuery->count();
        $photoAlbumQuery = $this->pagingCondition($photoAlbumQuery, $this->request->all());
        $data = $photoAlbumQuery->get();

        return $this->success([
            'list' => $data,
            'total' => $total,
        ]);
    }

    /**
     * Get album list
     * @RequestMapping(path="album_option", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     * })
     */
    public function albumOptionList()
    {
        $photoAlbumQuery = $this->photoAlbum->newQuery();
        $photoAlbumQuery = $photoAlbumQuery->select('id', 'album_name');
        $data = $photoAlbumQuery->get();

        return $this->success([
            'list' => $data,
        ]);
    }

    /**
     * @Explanation(content="Add album")
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
            'album_name'  => $postData['album_name'] ?? '',
            'album_desc'  => $postData['album_desc'] ?? '',
            'album_type'  => $postData['album_type'] ?? 1,
            'album_status' => $postData['album_status'] ?? 1,
            'album_author' => $postData['album_author'] ?? '',
            'album_cover' => $postData['album_cover'] ?? '',
            'album_question' => $postData['album_question'] ?? '',
            'album_answer' => $postData['album_answer'] ?? '',
            'album_sort' => $postData['album_sort'] ?? 99
        ];
        //Configuration verification
        $rules = [
            'album_name'  => 'required',
        ];
        //错误信息
        $message = [
            'album_name.required' => '[album_name] required',
        ];
        if ($params['album_type'] == 2) {
            $rules['album_question'] = 'required';
            $rules['album_answer'] = 'required';
            $message['album_question.required'] = '[album_question] required';
            $message['album_answer.required'] = '[album_answer] required';
        }
        $this->verifyParams($params, $rules, $message);

        $photoAlbumObj = new PhotoAlbum();
        $photoAlbumObj->album_name = $params['album_name'];
        $photoAlbumObj->album_desc = $params['album_desc'];
        $photoAlbumObj->album_type = $params['album_type'];
        $photoAlbumObj->album_status = $params['album_status'];
        $photoAlbumObj->album_author = $params['album_author'];
        $photoAlbumObj->album_cover = $params['album_cover'];
        $photoAlbumObj->album_question = $params['album_question'];
        $photoAlbumObj->album_answer = $params['album_answer'];
        $photoAlbumObj->album_sort = $params['album_sort'];
        if (!$photoAlbumObj->save()) $this->throwExp(StatusCode::ERR_EXCEPTION, 'Add album error');

        return $this->successByMessage('Add album successfully');
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
        $dictDataInfo = PhotoAlbum::findById($id);
        if (empty($dictDataInfo)) $this->throwExp(StatusCode::ERR_USER_ABSENT, 'Failure to obtain album information');

        return $this->success([
            'list' => $dictDataInfo
        ]);
    }

    /**
     * @Explanation(content="modify album information")
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
            'id' => $id,
            'album_name'  => $postData['album_name'] ?? '',
            'album_desc'  => $postData['album_desc'] ?? '',
            'album_type'  => $postData['album_type'] ?? 1,
            'album_status' => $postData['album_status'] ?? 1,
            'album_author' => $postData['album_author'] ?? '',
            'album_cover' => $postData['album_cover'] ?? '',
            'album_question' => $postData['album_question'] ?? '',
            'album_answer' => $postData['album_answer'] ?? '',
            'album_sort' => $postData['album_sort'] ?? 99
        ];
        //Configuration verification
        $rules = [
            'album_name'  => 'required',
            'id'  => 'required|integer',
        ];
        //Error message
        $message = [
            'album_name.required' => '[album_name] required',
            'id.required' => '[id] required',
            'id.integer' => '[id] must be integer',
        ];
        $this->verifyParams($params, $rules, $message);

        $photoAlbumObj = PhotoAlbum::findById($id);
        $photoAlbumObj->album_name = $params['album_name'];
        $photoAlbumObj->album_desc = $params['album_desc'];
        $photoAlbumObj->album_type = $params['album_type'];
        $photoAlbumObj->album_status = $params['album_status'];
        $photoAlbumObj->album_author = $params['album_author'];
        $photoAlbumObj->album_cover = $params['album_cover'];
        $photoAlbumObj->album_question = $params['album_question'];
        $photoAlbumObj->album_answer = $params['album_answer'];
        $photoAlbumObj->album_sort = $params['album_sort'];
        if (!$photoAlbumObj->save()) $this->throwExp(StatusCode::ERR_EXCEPTION, 'Modify album information errors');

        return $this->successByMessage('Modify album information successfully');
    }

    /**
     * @Explanation(content="Delete album information")
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
            if (!PhotoAlbum::whereIn('id', $idArr)->delete()) $this->throwExp(StatusCode::ERR_EXCEPTION, 'failed to delete');
        }else {
            if (!intval($id)) $this->throwExp(StatusCode::ERR_VALIDATION, 'Parameter error');
            if (!PhotoAlbum::destroy($id)) $this->throwExp(StatusCode::ERR_EXCEPTION, 'failed to delete');
        }

        return $this->successByMessage('Delete the album successfully');
    }

}
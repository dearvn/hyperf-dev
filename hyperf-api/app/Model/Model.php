<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Model;

use Hyperf\DbConnection\Model\Model as BaseModel;
use Hyperf\DbConnection\Traits\HasContainer;
use Hyperf\DbConnection\Traits\HasRepository;
use phpDocumentor\Reflection\Types\Void_;

/**
 * Class Model
 * @package App\Model
 * @Author YiYuan-Lin
 * @Date: 2021/2/6
 */
abstract class Model extends BaseModel
{
    use HasContainer;
    use HasRepository;

    /**
     * Obtain single data based on ID
     * @param int $id
     * @return array|\Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    static function findById($id)
    {
        if (empty($id)) return [];

        return static::query()->find($id);
    }

    /**
     * adding data
     * @param array $data
     * @return bool
     */
     static function add(array $data = []) : bool
    {
        if (empty($data)) return false;
        $model = new static;

        foreach ($data as $key => $value) {
            $model->{$key} = $value;
        }

        if (!$model->save()) return false;
        return true;
    }
}

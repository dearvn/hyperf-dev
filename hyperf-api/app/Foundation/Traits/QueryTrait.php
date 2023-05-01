<?php
namespace App\Foundation\Traits;

/**
 * Query base class
 * Trait QueryTrait
 * @package App\Foundation\Traits
 */
trait QueryTrait
{
    /**
     * Treatment of pagination conditions
     *
     * @param $query
     * @param $params
     * @return mixed
     */
    public function pagingCondition($query, $params)
    {
        $cur_page   = $params['cur_page'] ?? 1;
        $page_size  = $params['page_size'] ?? 20;

        $offset = ($cur_page- 1) * $page_size;
        $limit  = $page_size;
        $query = $query->offset($offset)->limit($limit);

        return $query;
    }
}

<?php

declare(strict_types=1);

namespace App\Model\Auth;

use Donjan\Permission\Models\Role as DonjanRole;

/**
 * Character model class
 * Class Role
 * @package App\Model\Auth
 * @Author YiYuan-Lin
 * @Date: 2021/1/21
 */
class Role extends DonjanRole
{
    /**
     * Disclaimer the role name of the super administrator
     */
    const SUPER_ADMIN = 'super_admin';

    /**
     * Get role information based on orange ID
     * @param $id
     * @return array|\Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    static function getOneByRoleId($id)
    {
        if (empty($id)) return [];

        $query = static::query();
        $query = $query->where('id', $id);

        return $query->first();
    }

}
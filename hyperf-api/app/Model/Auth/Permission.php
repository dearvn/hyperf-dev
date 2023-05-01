<?php

declare(strict_types=1);

namespace App\Model\Auth;

use Donjan\Permission\Models\Permission as DonjanPermission;

/**
 * Permissions model class
 * Class Permission
 * @package App\Model\Auth
 * @Author YiYuan-Lin
 * @Date: 2020/12/3
 */
class Permission extends DonjanPermission
{
    /**
    * Declaration permission type enumeration
    *1: Directory
    *2: Menu
    *3: interface/interface
    */
    const DIRECTORY_TYPE = 1;
    const MENU_TYPE = 2;
    const BUTTON_OR_API_TYPE = 3;

    /**
    *Declaration status enumeration
    *1: open
    *0: off
    */
    const ON_STATUS = 1;
    const OFF_STATUS = 0;

    /**
    *Declare whether to hide the display enumeration
    *1: yes
    *0: no
    */
    const IS_HIDDEN = 1;
    const IS_NOT_HIDDEN = 0;

    /**
     * Obtain the menu -like list of the user's corresponding menu
     * @param Object [User model object] $user
     * @return array
     */
    public static function getUserMenuList(Object $user) : array
    {
        $permissionList = self::getUserPermissions($user);
        $permissionList = objToArray($permissionList);
        $permissionList = array_column($permissionList, null, 'id');

        //Use the reference to transmit the recursive array
        $menuList = [];
        foreach($permissionList as $key => $value){
            if(isset($permissionList[$value['parent_id']])){
                $permissionList[$value['parent_id']]['child'][] = &$permissionList[$key];
            }else{
                $menuList[] = &$permissionList[$key];
            }
        }
        //Recursively filtering data that does not meet the conditions
        $menuList = static::checkPermissionFilter($menuList);
        return $menuList;
    }

    /**
     * Check whether the permissions need to be filtered
     * @param array $item
     * @return array
     */
    private static function checkPermissionFilter(array $item) : array
    {
        if (!empty($item)) {
            foreach ($item as $key => $value) {
                if ($value['status'] == self::OFF_STATUS) unset($item[$key]);
                if ($value['type'] == self::BUTTON_OR_API_TYPE) unset($item[$key]);
                if ($value['hidden'] == self::IS_HIDDEN) unset($item[$key]);
                if (!empty($item[$key]['child']))  {
                    $item[$key]['child'] = array_values(static::checkPermissionFilter($item[$key]['child']));
                }
            }
           return array_values($item);
        }
        return [];
    }

    /**
     * Get user ownership permissions
     * @param object [User model object] $user
     * @return array
     */
    public static function getUserPermissions(object $user) : array
    {
        $allPermissions = [];
        if (empty($user)) return $allPermissions;

        $superRoleHasPermission = Permission::query()->orderBy('sort', 'asc')->get()->toArray();
        $userHasPermission = objToArray($user->getAllPermissions());
        array_multisort(array_column($userHasPermission, 'sort'), SORT_ASC, $userHasPermission);

        //Determine whether the current login user is a super administrator, if so, return the ownership
        return $user->hasRole(Role::SUPER_ADMIN) ? $superRoleHasPermission : $userHasPermission;

    }

    /**
     * Obtain ownership (tree shape)
     * @return array
     */
    public static function getAllPermissionByTree() : array
    {
        //Obtain ownership list
        $permissionList = static::query()->select('id', 'parent_id', 'display_name', 'name')
            ->where('status', static::ON_STATUS)
            ->orderBy('sort', 'asc')
            ->get()->toArray();
        $permissionList = array_column($permissionList, null, 'id');

        $allPermission = [];
        foreach($permissionList as $key => $value){
            if(isset($permissionList[$value['parent_id']])){
                $permissionList[$value['parent_id']]['child'][] = &$permissionList[$key];
            }else{
                $allPermission[] = &$permissionList[$key];
            }
        }

        return $allPermission;
    }
}
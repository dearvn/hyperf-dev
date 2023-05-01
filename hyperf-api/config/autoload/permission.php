<?php

return [
    //Model settings
    'models' => [
        'user' => App\Model\Auth\User::class,
        'permission' => Donjan\Permission\Models\Permission::class,
        'role' => Donjan\Permission\Models\Role::class,
    ],
    //Table name setting
    'table_names' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
        'model_has_permissions' => 'model_has_permissions',
        'model_has_roles' => 'model_has_roles',
        'role_has_permissions' => 'role_has_permissions',
    ],
    'column_names' => [
        'model_morph_key' => 'model_id', //The main key of the associated template
    ],
    'display_permission_in_exception' => false,
    'cache' => [
        'expiration_time' => 86400,
        'key' => 'donjan.permission.cache',
        'model_key' => 'name',
        'store' => 'default',
    ],
];

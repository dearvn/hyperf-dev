<?php

declare(strict_types=1);

namespace App\Command;

use App\Model\Auth\User;
use App\Model\System\DictData;
use App\Model\System\DictType;
use App\Model\System\GlobalConfig;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Donjan\Permission\Models\Permission;
use Donjan\Permission\Models\Role;

/**
 * @Command
 */
class InitCommand extends HyperfCommand
{
    /**
     * Execute command line
     *
     * @var string
     */
    protected $name = 'init';

    public function configure()
    {
        parent::configure();
        $this->setHelp('HyperfCms Project initialization');
        $this->setDescription('HyperfCms Project initialization');
    }

    /**
     * Command execution method
     */
    public function handle()
    {
        //Initialize a default user and a super administrator role
        if (User::query()->where('username', 'admin@admin.com')->first()) {
            $this->line('The user has created' . PHP_EOL, 'warning');
        }

        $user = new User();
        $user->username = 'admin@admin.com';
        $user->password = md5('admin@admin.com');
        $user->status = User::STATUS_ON;
        $user->last_login = time();
        $user->desc = 'root';
        $user->mobile = '1800000000';
        $user->sex = User::SEX_BY_MALE;
        $user->email = 'admin@admin.com';
        $user->avatar = 'https://shmily-album.oss-cn-shenzhen.aliyuncs.com/admin_face/face' . rand(1, 10) .'.png';
        $user->save();
        $super_role = [
            'name' => 'super_admin',
            'guard_name' => 'web',
            'description' => 'Super administrator'
        ];

        $default_role = [
            'name' => 'default_admin',
            'guard_name' => 'web',
            'description' => 'Ordinary administrator'
        ];

        $tourist_role = [
            'name' => 'tourist_admin',
            'guard_name' => 'web',
            'description' => 'Tourist'
        ];

        //Create the default two characters
        $super_role = Role::create($super_role);
        $default_role = Role::create($default_role);
        $tourist_role = Role::create($tourist_role);

        //Creation permissions
        $permissionList = config('permissionData.permission_list');
        foreach ($permissionList as $permission) {
            if (empty(Permission::query()->find($permission['id']))) Permission::query()->insert($permission);
            $this->line('Add permission successfully----------------------------' . $permission['display_name']);
        }

        //Initialization dictionary data
        $dictTypeList = config('dictData.dict_type');
        foreach ($dictTypeList as $dictType) {
            if (empty(DictType::query()->find($dictType['dict_id']))) DictType::query()->insert($dictType);
        }
        $dictDataList = config('dictData.dict_data');
        foreach ($dictDataList as $dictData) {
            if (empty(DictData::query()->find($dictData['dict_code']))) DictData::query()->insert($dictData);
        }
        $this->line('Initialized dictionary data data is successful', 'info');

        $globalConfigList = config('globalConfig.global_config');
        foreach ($globalConfigList as $globalConfig) {
            if (empty(GlobalConfig::query()->find($globalConfig['id']))) GlobalConfig::query()->insert($globalConfig);
        }
        $this->line('Initialization global parameter successful', 'info');

        //Add the default role until the default user
        $user->assignRole($super_role->name);
        // Through the built -in method LINE output hello hyperf.
        $this->line('Initialize users successfully' . PHP_EOL . 'Default username：admin@admin.com' . PHP_EOL . 'default password：admin@admin.com' . PHP_EOL, 'info');
    }
}
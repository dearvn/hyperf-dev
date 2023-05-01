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
class InitDataSeederCommand extends HyperfCommand
{
    /**
     * Execute command line
     *
     * @var string
     */
    protected $name = 'init:data_seeder';

    public function configure()
    {
        parent::configure();
        $this->setHelp('Initialization data, permission data, dictionary data');
        $this->setDescription('Initialization data, permission data, dictionary data');
    }

    /**
     * Command execution method
     */
    public function handle()
    {
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
        $this->line('Migration dictionary data data is successful', 'info');
    }
}
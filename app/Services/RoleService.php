<?php 

namespace App\Services;

use App\Models\Role;
use App\Services\Core\BaseModelService;

class RoleService extends BaseModelService
{
    public function model(): string
    {
        return Role::class;
    }
}
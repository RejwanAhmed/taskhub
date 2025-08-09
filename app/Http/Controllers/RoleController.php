<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected RoleService $rolesService;

    public function __construct(RoleService $rolesService)
    {
        $this->rolesService = $rolesService;
    }
}

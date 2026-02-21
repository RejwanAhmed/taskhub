<?php

namespace App\Repositories\Eloquent;

use App\Models\Organization;
use App\Repositories\Contracts\OrganizationRepositoryInterface;

class OrganizationRepository implements OrganizationRepositoryInterface
{
    // Implement methods here

    protected $model;

    public function __construct(Organization $model)
    {
        $this->model = $model;
    }
    
    public function getUserOrganizations($user)
    {
        $organizations= $user->organizations()->withCount('members')->withCount('tasks')->get();
        return $organizations;
        // $organizations = $this->model->members()
    }
}

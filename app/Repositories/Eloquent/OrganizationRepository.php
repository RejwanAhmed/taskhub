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
    }

    public function create($validatedData)
    {
        return $this->model::create($validatedData);
    }

    public function attachOwner($organization, $userId)
    {
        $organization->members()->attach($userId, [
            'joined_at' => now(),
            'role' => 'owner'
        ]);
    }

    public function update(Organization $organization, $validatedData)
    {
        $organization->update($validatedData);
        return $organization;
    }

    public function delete(Organization $organization)
    {
        $organization->delete();
        return true;
    }
}

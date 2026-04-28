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

    public function switch(Organization $organization, $authUser)
    {
        $authUser->update(['current_organization_id' => $organization->id]);
        return true;
    }

    public function getOrganizationMembers(Organization $organization)
    {
        return $organization->members()
            ->select('users.id', 'users.email', 'users.name', 'users.avatar', 'users.bio')
            ->get();
    }

    public function isMember(Organization $organization, $email)
    {
        return $organization->members()->where('email', $email)->exists();
    }

    public function attachUser(Organization $organization, $userId, $role)
    {
        $organization->members()->attach($userId, [
            'joined_at' => now(),
            'role' => $role
        ]);
    }
}

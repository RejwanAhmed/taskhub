<?php

namespace App\Repositories\Eloquent;

use App\Models\Organization;
use App\Models\User;
use App\Repositories\Contracts\OrganizationRepositoryInterface;

class OrganizationRepository implements OrganizationRepositoryInterface
{
    // Implement methods here

    protected $model;

    public function __construct(Organization $model)
    {
        $this->model = $model;
    }
    
    public function getUserOrganizations(User $user)
    {
        $organizations= $user->organizations()->withCount('members')->withCount('tasks')->get();
        return $organizations;
    }

    public function create(array $data)
    {
        return $this->model::create($data);
    }

    public function attachOwner(Organization $organization, int $userId)
    {
        $organization->members()->attach($userId, [
            'joined_at' => now(),
            'role' => 'owner'
        ]);
    }

    public function update(Organization $organization, array $data)
    {
        $organization->update($data);
        return $organization;
    }

    public function delete(Organization $organization)
    {
        $organization->delete();
        return true;
    }

    public function switch(Organization $organization, User $user)
    {
        $user->update(['current_organization_id' => $organization->id]);
        return true;
    }

    public function getOrganizationMembers(Organization $organization)
    {
        return $organization->members()
            ->select('users.id', 'users.email', 'users.name', 'users.avatar', 'users.bio')
            ->get();
    }

    public function getCurrentOrganization(int $orgId)
    {
        return $this->model::findOrFail($orgId);
    }

    public function isMember(Organization $organization, string $email)
    {
        return $organization->members()->where('email', $email)->exists();
    }

    public function attachUser(Organization $organization, int $userId, string $role)
    {
        $organization->members()->attach($userId, [
            'joined_at' => now(),
            'role' => $role
        ]);
    }

    public function getActiveUsers(Organization $organization)
    {
        return $organization->activeUsers()->get(['users.id', 'users.name']);
    }
}

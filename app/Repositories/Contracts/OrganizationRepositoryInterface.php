<?php

namespace App\Repositories\Contracts;

use App\Models\Organization;
use App\Models\User;

interface OrganizationRepositoryInterface
{
    // Define your methods here

    public function getUserOrganizations(User $user);
    public function create(array $data);
    public function attachOwner(Organization $organization, int $userId);
    public function update(Organization $organization, array $data);
    public function delete(Organization $organization);
    public function switch(Organization $organization, User $user);
    public function getOrganizationMembers(Organization $organization);
    public function isMember(Organization $organization, string $email);
    public function attachUser(Organization $organization, int $userId, string $role);
    public function getCurrentOrganization(int $orgId);
    public function getActiveUsers(Organization $organization);
}

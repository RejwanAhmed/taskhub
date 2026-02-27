<?php

namespace App\Repositories\Contracts;

use App\Models\Organization;

interface OrganizationRepositoryInterface
{
    // Define your methods here

    public function getUserOrganizations($user);
    public function create($validatedData);
    public function attachOwner($organization, $userId);
    public function update(Organization $organization, $validatedData);
    public function delete(Organization $organization);
    public function switch(Organization $organization, $authUser);
}

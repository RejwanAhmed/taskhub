<?php

namespace App\Repositories\Contracts;

interface OrganizationRepositoryInterface
{
    // Define your methods here

    public function getUserOrganizations($user);
    public function create($validatedData);
    public function attachOwner($organization, $userId);
}

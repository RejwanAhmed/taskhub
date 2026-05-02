<?php

namespace App\Repositories\Contracts;

use App\Models\Organization;

interface ProjectRepositoryInterface
{
    public function getProjects(Organization $organization);
}

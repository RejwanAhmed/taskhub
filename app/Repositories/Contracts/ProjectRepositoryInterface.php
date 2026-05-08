<?php

namespace App\Repositories\Contracts;

use App\Models\Organization;
use App\Models\Project;

interface ProjectRepositoryInterface
{
    public function getProjects(Organization $organization);
    public function attachOwner(Project $project, $userId);
}

<?php

namespace App\Repositories\Contracts;

use App\Models\Organization;
use App\Models\Project;
use App\Models\User;

interface ProjectRepositoryInterface
{
    public function getProjects(Organization $organization);
    public function attachOwner(Project $project, $userId);
    public function isProjectOwner(Project $project, User $user);
    public function getProjectDetails(Project $project);
}

<?php

namespace App\Repositories\Contracts;

use App\Models\Organization;
use App\Models\Project;
use App\Models\User;

interface ProjectRepositoryInterface
{
    public function getProjects(Organization $organization);
    public function createProject(array $data);
    public function attachOwner(Project $project, int $userId);
    public function updateProject(Project $project, array $data);
    public function isProjectOwner(Project $project, User $user);
    public function getProjectDetails(Project $project);
    public function syncMembers(Project $project, array $syncData);
}

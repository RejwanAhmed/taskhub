<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Support\OrganizationSession;

class ProjectPolicy
{
    public $projectRepo;

    public function __construct(ProjectRepositoryInterface $projectRepo)
    {
        $this->projectRepo = $projectRepo;
    }

    public function update(User $user, Project $project)
    {
        return OrganizationSession::getCurrentOrg() === $project->organization_id && $this->projectRepo->isProjectOwner($project, $user);
    }
}

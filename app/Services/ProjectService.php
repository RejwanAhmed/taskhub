<?php 

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use App\Repositories\Contracts\OrganizationRepositoryInterface;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ProjectService
{
    protected $projectRepo;
    protected $organizationRepo;

    public function __construct(ProjectRepositoryInterface $projectRepo, OrganizationRepositoryInterface $organizationRepo)
    {
        $this->projectRepo = $projectRepo;
        $this->organizationRepo = $organizationRepo;
    }

    public function getProjects(int $orgId): Collection
    {
        $organization = $this->organizationRepo->getCurrentOrganization($orgId);
        return $this->projectRepo->getProjects($organization);
    }

    public function createProject(User $user, int $orgId, array $data): void
    {
        DB::transaction(function() use ($user, $orgId, $data) {
            $data = array_merge($data, [
                'organization_id' => $orgId,
                'created_by' => $user->id,
            ]);
            $project = $this->projectRepo->createProject($data);
            $this->projectRepo->attachOwner($project, $userId);
        });
    }

    public function updateProject(Project $project, array $data): void
    {
        $this->projectRepo->updateProject($project, $data);
    }

    public function getProjectDetails(Project $project): Project
    {
        return $this->projectRepo->getProjectDetails($project);
    }

    public function assignMembers(Project $project, array $members, User $user): void
    {
        $syncData = [];
        $userId = $user->id;
        foreach ($members['members'] as $member) {
            $syncData[$member['id']] = ['role' => $member['role'], 'added_by' => $userId];
        }

        $this->projectRepo->syncMembers($project, $syncData);
    }
}
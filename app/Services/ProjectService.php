<?php 

namespace App\Services;

use App\Models\Project;
use App\Repositories\Contracts\OrganizationRepositoryInterface;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Services\Core\BaseModelService;
use Illuminate\Support\Facades\DB;

class ProjectService extends BaseModelService
{
    protected $projectRepo;
    protected $organizationRepo;

    public function model(): string
    {
        return Project::class;
    }

    public function __construct(ProjectRepositoryInterface $projectRepo, OrganizationRepositoryInterface $organizationRepo)
    {
        $this->projectRepo = $projectRepo;
        $this->organizationRepo = $organizationRepo;
    }

    public function getProjects($organizationId)
    {
        $organization = $this->organizationRepo->getCurrentOrganization($organizationId);
        return $this->projectRepo->getProjects($organization);
    }

    public function createProject($userId, $organizationId, $validatedData)
    {
        return DB::transaction(function() use ($userId, $organizationId, $validatedData) {
            $validatedData = array_merge($validatedData, [
                'organization_id' => $organizationId,
                'created_by' => $userId,
            ]);
            $project = $this->model()::create($validatedData);
            $this->projectRepo->attachOwner($project, $userId);
        });
    }

    public function updateProject(Project $project, $validatedData)
    {
        $project->update($validatedData);
    }

    public function getProjectDetails(Project $project)
    {
        return $this->projectRepo->getProjectDetails($project);
    }
}
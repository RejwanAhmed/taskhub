<?php 

namespace App\Services;

use App\Models\Project;
use App\Repositories\Contracts\OrganizationRepositoryInterface;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Services\Core\BaseModelService;

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
}
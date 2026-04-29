<?php 

namespace App\Services;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Services\Core\BaseModelService;

class ProjectService extends BaseModelService
{
    protected $projectRepo;

    public function model(): string
    {
        return Project::class;
    }

    public function __construct(ProjectRepositoryInterface $projectRepo)
    {
        $this->projectRepo = $projectRepo;
    }
}
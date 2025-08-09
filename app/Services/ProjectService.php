<?php 

namespace App\Services;

use App\Models\Project;
use App\Services\Core\BaseModelService;

class ProjectService extends BaseModelService
{
    public function model(): string
    {
        return Project::class;
    }
}
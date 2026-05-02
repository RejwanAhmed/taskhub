<?php

namespace App\Repositories\Eloquent;

use App\Models\Organization;
use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;

class ProjectRepository implements ProjectRepositoryInterface
{
    protected $model;

    public function model(Project $model)
    {
        $this->model = $model;
    }

    public function getProjects(Organization $organization)
    {
        return $organization->projects()
            ->withCount(['members'])
            ->withCount([
                'tasks',
                'tasks as completed_tasks_count' => function ($query) {
                    $query->where('status', 'completed');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}

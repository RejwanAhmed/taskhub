<?php

namespace App\Repositories\Eloquent;

use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
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

    public function attachOwner(Project $project, $userId)
    {
        $project->members()->attach($userId, [
            'role' => 'owner'
        ]);
    }

    public function isProjectOwner(Project $project, User $user)
    {
        return $project->members()->where('user_id', $user->id)->where('role', 'owner')->exists();
    }

    public function getProjectDetails(Project $project)
    {
        return $project->load([
            'members:id,name'
        ])->loadCount([
            'tasks',
            'tasks as completed_tasks_count' => fn ($q) => $q->where('status', 'completed')
        ]);
    }
}

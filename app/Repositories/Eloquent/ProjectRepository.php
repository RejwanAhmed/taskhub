<?php

namespace App\Repositories\Eloquent;

use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use App\Repositories\Contracts\ProjectRepositoryInterface;

class ProjectRepository implements ProjectRepositoryInterface
{
    protected $model;

    public function __construct(Project $model)
    {
        $this->model = $model;
    }

    public function getProjects(Organization $organization)
    {
        return $organization->projects()
            ->withCount([
                'members',
                'tasks',
                'tasks as completed_tasks_count' => function ($query) {
                    $query->where('status', 'completed');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function createProject(array $data)
    {
        return $this->model::create($data);
    }

    public function attachOwner(Project $project, int $userId)
    {
        $project->members()->attach($userId, [
            'role' => 'owner'
        ]);
    }

    public function updateProject(Project $project, array $data)
    {
        $project->update($data);
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

    public function syncMembers(Project $project, array $syncData)
    {
        $project->members()->sync($syncData);
    }
}

<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class TaskService
{
    protected $taskRepo;

    public function __construct(TaskRepositoryInterface $taskRepo)
    {
        $this->taskRepo = $taskRepo;
    }

    public function getProjectTasks(Project $project): Collection
    {
        return $this->taskRepo->getProjectTasks($project);
    }

    public function createTask(Project $project, User $user, int $orgId, array $data): void
    {
        DB::transaction(function () use ($project, $user, $orgId, $data) {
            if ($project->organization_id !== $orgId) 
                throw new BusinessException('This project does not belong to your organization.');

            $tagIds = $data['tags'] ?? [];
            $validatedData = [
                'organization_id'   => $orgId,
                'project_id'        => $project->id,
                'title'             => $data['title'],
                'description'       => $data['description'] ?? null,
                'status'            => $data['status'],
                'priority'          => $data['priority'],
                'task_type_id'      => $data['task_type_id'] ?? null,
                'assigned_to'       => $data['assigned_to'] ?? null,
                'due_date'          => $data['due_date'] ?? null,
                'estimated_hours'   => $data['estimated_hours'] ?? null,
                'created_by'        => $user->id,   
            ];
            $task = $this->taskRepo->createTask($validatedData);

            if (!empty($tagIds))
                $this->taskRepo->syncTags($task, $tagIds);
        });
    }

    public function updateTask(Project $project, Task $task, int $orgId, array $data): void
    {
        DB::transaction(function () use ($project, $task, $orgId, $data) {
            if ($task->organization_id !== $orgId) 
                throw new BusinessException('This project does not belong to your organization.');
            else if ($task->project_id !== $project->id)
                throw new BusinessException('This task does not belong to this project.');

            $tagIds = $data['tags'] ?? [];
            unset($data['tags']);

            $this->taskRepo->updateTask($task, $data);
            $this->taskRepo->syncTags($task, $tagIds);
        });
    }

    public function deleteTask(Project $project, Task $task, int $orgId): void
    {
        if ($task->organization_id !== $orgId) 
            throw new BusinessException('This task does not belong to your organization.');
        else if ($task->project_id !== $project->id)
            throw new BusinessException('This task does not belong to this project.');

        $this->taskRepo->deleteTask($task);
    }

    public function getSubtasks(Task $task): Collection
    {
        return $this->taskRepo->getSubtasks($task);
    }

    public function createSubtask(Project $project, Task $parentTask, User $user, int $orgId, array $data): void
    {
        if ($project->organization_id !== $orgId) 
            throw new BusinessException('This project does not belong to your organization.');
        else if ($parentTask->project_id !== $project->id)
            throw new BusinessException('This task does not belong to this project.');
        else if ($parentTask->parent_task_id !== null)
            throw new BusinessException('Subtasks cannot have nested subtasks.');

        $validatedData = [
            'organization_id' => $project->organization_id,
            'project_id'      => $project->id,
            'parent_task_id'  => $parentTask->id,
            'title'           => $data['title'],
            'description'     => $data['description'] ?? null,
            'priority'        => $data['priority'],
            'task_type_id'    => $data['task_type_id'] ?? null,
            'status'          => 'to_do',
            'assigned_to'     => $data['assigned_to'] ?? null,
            'due_date'        => $data['due_date'] ?? null,
            'estimated_hours' => $data['estimated_hours'] ?? null,
            'created_by'      => $user->id,
        ];

        $this->taskRepo->createTask($validatedData);
    }
}
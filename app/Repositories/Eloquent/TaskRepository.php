<?php

namespace App\Repositories\Eloquent;

use App\Models\Project;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;

class TaskRepository implements TaskRepositoryInterface
{
    // Implement methods here
    protected $model;

    public function __construct(Task $model)
    {
        $this->model = $model;
    }

    public function getProjectTasks(Project $project)
    {
        return $project->tasks()
            ->whereNull('parent_task_id')
            ->with([
                'assignee:id,name,email',
                'taskType:id,name,color,icon',
                'tags:id,name,color',
            ])
            ->withCount(['subTasks', 'comments', 'attachments'])
            ->orderBy('position')
            ->get();
    }

    public function createTask(array $data)
    {
        return $this->model::create($data);
    }

    public function syncTags(Task $task, array $tagIds)
    {
        $task->tags()->sync($tagIds);
    }

    public function updateTask(Task $task, array $data)
    {
        $task->update($data);
    }

    public function deleteTask(Task $task)
    {
        $task->delete();
    }

    public function getSubtasks(Task $task)
    {
        return $task->subTasks()
            ->with(['assignee:id,name'])
            ->withCount(['comments', 'attachments'])
            ->orderBy('created_at', 'asc')->get();
    }
}

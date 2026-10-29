<?php

namespace App\Repositories\Contracts;

use App\Models\Project;
use App\Models\Task;

interface TaskRepositoryInterface
{
    // Define your methods here
    public function getProjectTasks(Project $project);
    public function createTask(array $data);
    public function syncTags(Task $task, array $tagIds);
    public function updateTask(Task $task, array $data);
    public function deleteTask(Task $task);
    public function getSubtasks(Task $task);
    public function reorderTask(Task $task, string $status, int $position);
    public function getColumnTasks(Project $project, string $status, int $excludeTaskId);
}

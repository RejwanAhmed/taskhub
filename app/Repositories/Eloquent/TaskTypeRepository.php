<?php

namespace App\Repositories\Eloquent;

use App\Models\Organization;
use App\Models\TaskType;
use App\Repositories\Contracts\TaskTypeRepositoryInterface;

class TaskTypeRepository implements TaskTypeRepositoryInterface
{
    protected $model;
    public function __construct(TaskType $model)
    {
        $this->model = $model;
    }

    public function getTaskTypes(Organization $organization)
    {
        return $organization->taskTypes()->get();
    }

    public function createTaskType(array $data)
    {
        return $this->model::create($data);
    }

    public function removeDefault(Organization $organization, TaskType $taskType = null)
    {
        return $organization->taskTypes()
            ->where('is_default', true)
            ->when($taskType, fn($q) => $q->where('id', '!=', $taskType->id))
            ->update(['is_default' => false]);
    }
    
    public function updateTaskType(TaskType $taskType, array $data)
    {
        return $taskType->update($data);
    }

    public function deleteTaskType(TaskType $taskType)
    {
        return $taskType->delete();
    }
}

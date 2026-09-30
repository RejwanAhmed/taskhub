<?php

namespace App\Repositories\Eloquent;

use App\Models\Organization;
use App\Models\TaskType;
use App\Repositories\Contracts\TaskTypeRepositoryInterface;

class TaskTypeRepository implements TaskTypeRepositoryInterface
{
    public function model()
    {
        return TaskType::class;
    }

    public function getTaskTypes(Organization $organization)
    {
        return $organization->taskTypes()->get();
    }

    public function createTaskType($validatedData)
    {
        return $this->model()::create($validatedData);
    }

    public function removeDefault(Organization $organization)
    {
        return $organization->taskTypes()->where('is_default', true)->update(['is_default' => false]);
    }
    
    public function updateTaskType(TaskType $taskType, $validatedData)
    {
        return $taskType->update($validatedData);
    }

    public function deleteTaskType(TaskType $taskType)
    {
        return $taskType->delete();
    }
}

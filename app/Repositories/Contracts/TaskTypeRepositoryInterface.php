<?php

namespace App\Repositories\Contracts;

use App\Models\Organization;
use App\Models\TaskType;

interface TaskTypeRepositoryInterface
{
    public function getTaskTypes(Organization $organization);
    public function createTaskType($validatedData);
    public function removeDefault(Organization $organization);
    public function updateTaskType(TaskType $taskType, $validatedData);
    public function deleteTaskType(TaskType $taskType);
}

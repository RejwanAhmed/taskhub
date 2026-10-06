<?php

namespace App\Repositories\Contracts;

use App\Models\Organization;
use App\Models\TaskType;

interface TaskTypeRepositoryInterface
{
    public function getTaskTypes(Organization $organization);
    public function createTaskType(array $data);
    public function removeDefault(Organization $organization, TaskType $taskType = null);
    public function updateTaskType(TaskType $taskType, array $data);
    public function deleteTaskType(TaskType $taskType);
}

<?php 

namespace App\Services;

use App\Models\Task;
use App\Services\Core\BaseModelService;

class TaskService extends BaseModelService
{
    public function model(): string
    {
        return Task::class;
    }
}
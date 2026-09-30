<?php

namespace App\Services;

use App\Models\TaskType;
use App\Repositories\Contracts\OrganizationRepositoryInterface;
use App\Repositories\Contracts\TaskTypeRepositoryInterface;
use App\Services\Core\BaseModelService;
use Illuminate\Support\Facades\DB;

class TaskTypeService extends BaseModelService
{
    protected $taskTypeRepo;
    protected $organizationRepo;

    public function model(): string
    {
        return TaskType::class;
    }

    public function __construct(TaskTypeRepositoryInterface $taskTypeRepo, OrganizationRepositoryInterface $organizationRepo)
    {
        $this->taskTypeRepo = $taskTypeRepo;
        $this->organizationRepo = $organizationRepo;
    }

    public function getTaskTypes($organizationId)
    {
        $organization = $this->organizationRepo->getCurrentOrganization($organizationId);
        return $this->taskTypeRepo->getTaskTypes($organization);
    }

    public function createTaskType($validatedData)
    {
        return DB::transaction(function () use ($validatedData) {
            $organization = $this->organizationRepo->getCurrentOrganization($validatedData['organization_id']);

            if($validatedData['is_default']) {
                $this->taskTypeRepo->removeDefault($organization);
            }
            $this->taskTypeRepo->createTaskType($validatedData);
        });
    }

    public function updateTaskType(TaskType $taskType, $validatedData)
    {
        return DB::transaction(function () use ($taskType, $validatedData) {
            
            if($validatedData['is_default']) {
                $organization = $this->organizationRepo->getCurrentOrganization($validatedData['organization_id']);
                $this->taskTypeRepo->removeDefault($organization);
            }

            $this->taskTypeRepo->updateTaskType($taskType, $validatedData);
        });
    }

    public function deleteTaskType(TaskType $taskType)
    {
        return $this->taskTypeRepo->deleteTaskType($taskType);
    }
}
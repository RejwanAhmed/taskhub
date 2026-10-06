<?php

namespace App\Services;

use App\Models\TaskType;
use App\Repositories\Contracts\OrganizationRepositoryInterface;
use App\Repositories\Contracts\TaskTypeRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TaskTypeService
{
    protected $taskTypeRepo;
    protected $organizationRepo;

    public function __construct(TaskTypeRepositoryInterface $taskTypeRepo, OrganizationRepositoryInterface $organizationRepo)
    {
        $this->taskTypeRepo = $taskTypeRepo;
        $this->organizationRepo = $organizationRepo;
    }

    public function getTaskTypes(int $orgId): Collection
    {
        $organization = $this->organizationRepo->getCurrentOrganization($orgId);
        return $this->taskTypeRepo->getTaskTypes($organization);
    }

    public function createTaskType(int $orgId, array $data): void
    {
        DB::transaction(function () use ($orgId, $data) {
            $organization = $this->organizationRepo->getCurrentOrganization($orgId);
            $data = array_merge($data, [
                'organization_id' => $orgId,
            ]);

            if($data['is_default']) {
                $this->taskTypeRepo->removeDefault($organization);
            }
            $this->taskTypeRepo->createTaskType($data);
        });
    }

    public function updateTaskType(TaskType $taskType, int $orgId, array $data): void
    {
        DB::transaction(function () use ($taskType, $orgId, $data) {
            
            if($data['is_default']) {
                $organization = $this->organizationRepo->getCurrentOrganization($orgId);
                $this->taskTypeRepo->removeDefault($organization, $taskType);
            }

            $this->taskTypeRepo->updateTaskType($taskType, $data);
        });
    }

    public function deleteTaskType(TaskType $taskType): bool
    {
        return $this->taskTypeRepo->deleteTaskType($taskType);
    }
}
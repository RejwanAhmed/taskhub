<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Http\Requests\TaskType\CreateTaskTypeRequest;
use App\Http\Requests\TaskType\UpdateTaskTypeRequest;
use App\Models\TaskType;
use App\Services\TaskTypeService;
use App\Support\OrganizationSession;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TaskTypeController extends Controller
{
    protected TaskTypeService $taskTypeService;
    protected $currentOrganizationId;

    public function __construct(TaskTypeService $taskTypeService)
    {
        $this->taskTypeService = $taskTypeService;
        $this->currentOrganizationId = OrganizationSession::getCurrentOrg();
    }

    public function index()
    {
        try {
            $taskTypes = $this->taskTypeService->getTaskTypes($this->currentOrganizationId);
            $responseData = [
                'taskTypes' => $taskTypes
            ];

            return Inertia::render('TaskType/Index', $responseData);
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            abort(500, Constants::DEFAULTMESSAGE);
        }
    }

    public function store(CreateTaskTypeRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['organization_id'] = $this->currentOrganizationId;
            $this->taskTypeService->createTaskType($validatedData);
            $message = 'New Task Type Created Successfully';
            return Redirect::route('task-types.index')->with(Constants::SUCCESS, $message);
        } catch (Exception $e) {
            Log::info('Task Type creation failed = '. $e->getMessage());
            return Redirect::route('task-types.index')->with(Constants::ERROR, Constants::DEFAULTMESSAGE);
        }
    }

    public function update(UpdateTaskTypeRequest $request, TaskType $taskType)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['organization_id'] = $this->currentOrganizationId;
            $this->taskTypeService->updateTaskType($taskType, $validatedData);
            $message = 'Task Type Updated Successfully';
            return Redirect::route('task-types.index')->with(Constants::SUCCESS, $message);
        } catch (Exception $e) {
            Log::info('Task Type update failed = ' . $e->getMessage());
            return Redirect::route('task-types.index')->with(Constants::ERROR, Constants::DEFAULTMESSAGE);
        }
    }

    public function destroy(TaskType $taskType)
    {
        try {
            $this->taskTypeService->deleteTaskType($taskType);
            $message = 'Task Type Deleted Successfully';
            return Redirect::route('task-types.index')->with(Constants::SUCCESS, $message);
        } catch (Exception $e) {
            Log::info('Task Type deleted failed = ' . $e->getMessage());
            return Redirect::route('task-types.index')->with(Constants::ERROR, Constants::DEFAULTMESSAGE);
        }
    }
}

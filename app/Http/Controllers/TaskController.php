<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Exceptions\BusinessException;
use App\Http\Requests\Task\CreateReorderRequest;
use App\Http\Requests\Task\CreateSubTaskRequest;
use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use App\Support\OrganizationSession;
use Exception;
use Illuminate\Support\Facades\Log;


class TaskController extends Controller
{
    protected TaskService $taskService;
    
    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(Project $project)
    {
        try {
            $tasks = $this->taskService->getProjectTasks($project);
            $responseData = [
                'tasks' => $tasks
            ];
            return response()->json($responseData);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([Constants::ERROR => Constants::DEFAULTMESSAGE], 500);
        }
    }

    public function store(CreateTaskRequest $request, Project $project)
    {
        try {
            $validatedData =$request->validated();
            $orgId = OrganizationSession::getCurrentOrg();
            $this->taskService->createTask($project, auth()->user(), $orgId, $validatedData);
            $message = 'New Task Created Successfully';
            return response()->json([Constants::SUCCESS => $message], 201);
        } catch (BusinessException $e) {
            return response()->json([Constants::ERROR => $e->getMessage()], 422);
        } catch (Exception $e) {
            Log::error('Task creation failed: ' . $e->getMessage());
            return response()->json([Constants::ERROR => Constants::DEFAULTMESSAGE], 500);
        }
    }

    public function update(UpdateTaskRequest $request, Project $project, Task $task)
    {
        try {
            $validatedData = $request->validated();
            $orgId = OrganizationSession::getCurrentOrg();
            $this->taskService->updateTask($project, $task, $orgId, $validatedData);
            $message = 'Task updated successfully';
            return response()->json([Constants::SUCCESS => $message], 201);
        } catch (BusinessException $e) {
            return response()->json([Constants::ERROR => $e->getMessage()], 422);
        } catch (Exception $e) {
            Log::error('Task update failed: ' . $e->getMessage());
            return response()->json([Constants::ERROR => Constants::DEFAULTMESSAGE], 500);
        }
    }

    public function destroy(Project $project, Task $task)
    {
        try {
            $orgId = OrganizationSession::getCurrentOrg();
            $this->taskService->deleteTask($project, $task, $orgId);
            $message = 'Task Deleted Successfully';
            return response()->json([Constants::SUCCESS => $message], 201);
        } catch (BusinessException $e) {
            return response()->json([Constants::ERROR => $e->getMessage()], 422);
        } catch (Exception $e) {
            Log::error('Task deletion failed: ' . $e->getMessage());
            return response()->json([Constants::ERROR => Constants::DEFAULTMESSAGE], 500);
        }
    }

    public function getSubtasks(Project $project, Task $task)
    {
        try {
            $subtasks = $this->taskService->getSubtasks($task);
            $responseData = [
                'subtasks' => $subtasks
            ];
            return response()->json($responseData);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => Constants::DEFAULTMESSAGE], 500);
        }
    }

    public function storeSubtask(CreateSubTaskRequest $request, Project $project, Task $task)
    {
        try {
            $validatedData = $request->validated();
            $orgId = OrganizationSession::getCurrentOrg();
            $this->taskService->createSubtask($project, $task, auth()->user(), $orgId, $validatedData);
            $message = 'Subtask created successfully';
            return response()->json([Constants::SUCCESS => $message], 201);
        } catch (BusinessException $e) {
            return response()->json([Constants::ERROR => $e->getMessage()], 422);
        } catch (Exception $e) {
            Log::error('Subtask creation failed: ' . $e->getMessage());
            return response()->json([Constants::ERROR => Constants::DEFAULTMESSAGE], 500);
        }
    }

    public function updateSubtask(UpdateTaskRequest $request, Project $project, Task $task, Task $subtask)
    {
        try {
            $validatedData = $request->validated();
            $orgId = OrganizationSession::getCurrentOrg();
            $this->taskService->updateTask($project, $subtask, $orgId, $validatedData);
            $message = 'Subtask Updated Successfully';
            return response()->json([Constants::SUCCESS => $message]);
        } catch (BusinessException $e) {
            return response()->json([Constants::ERROR => $e->getMessage()], 422);
        } catch (Exception $e) {
            Log::error('Subtask update failed: ' . $e->getMessage());
            return response()->json([Constants::ERROR => Constants::DEFAULTMESSAGE], 500);
        }
    }

    public function destroySubtask(Project $project, Task $task, Task $subtask)
    {
        try {
            $orgId = OrganizationSession::getCurrentOrg();
            $this->taskService->deleteTask($project, $subtask, $orgId);
            $message = 'Subtask deleted successfully';
            return response()->json([Constants::SUCCESS => $message], 201);
        } catch (BusinessException $e) {
            return response()->json([Constants::ERROR => $e->getMessage()], 422);
        } catch (Exception $e) {
            Log::error('Subtask deletion failed: ' . $e->getMessage());
            return response()->json([Constants::ERROR => Constants::DEFAULTMESSAGE], 500);
        }
    }

    public function reorder(CreateReorderRequest $request, Project $project)
    {
        try {
            $validatedData = $request->validated();
            $orgId = OrganizationSession::getCurrentOrg();
            $this->taskService->reorderTask($project, $orgId, $validatedData);
            $message = 'Task reordered successfully';
            return response()->json([Constants::SUCCESS => $message]);
        } catch (BusinessException $e) {
            return response()->json([Constants::ERROR => $e->getMessage()], 422);
        } catch (Exception $e) {
            Log::error('Task reorder failed: ' . $e->getMessage());
            return response()->json([Constants::ERROR => Constants::DEFAULTMESSAGE], 500);
        }
    }
}

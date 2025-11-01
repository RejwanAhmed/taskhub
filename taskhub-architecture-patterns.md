# TaskHub - Architecture & Design Patterns Documentation

## Table of Contents
1. [Application Architecture Overview](#application-architecture-overview)
2. [Folder Structure](#folder-structure)
3. [Design Patterns Implementation](#design-patterns-implementation)
4. [Laravel Best Practices](#laravel-best-practices)
5. [Code Examples](#code-examples)
6. [Testing Strategy](#testing-strategy)

---

## 1. Application Architecture Overview

TaskHub follows a **Layered Architecture** pattern with clear separation of concerns:

```
┌─────────────────────────────────────────────────────────────────┐
│                     PRESENTATION LAYER                           │
│  (Vue 3 + Inertia.js Components, Blade Templates)               │
└────────────────────────┬────────────────────────────────────────┘
                         │
┌────────────────────────▼────────────────────────────────────────┐
│                     CONTROLLER LAYER                             │
│  (HTTP Controllers - Thin, delegate to services)                │
└────────────────────────┬────────────────────────────────────────┘
                         │
┌────────────────────────▼────────────────────────────────────────┐
│                     SERVICE LAYER                                │
│  (Business Logic, Orchestration, Validation)                    │
└────────────────────────┬────────────────────────────────────────┘
                         │
┌────────────────────────▼────────────────────────────────────────┐
│                     REPOSITORY LAYER                             │
│  (Data Access, Database Queries)                                │
└────────────────────────┬────────────────────────────────────────┘
                         │
┌────────────────────────▼────────────────────────────────────────┐
│                     MODEL LAYER                                  │
│  (Eloquent Models, Relationships)                               │
└────────────────────────┬────────────────────────────────────────┘
                         │
┌────────────────────────▼────────────────────────────────────────┐
│                     DATABASE LAYER                               │
│  (MySQL/PostgreSQL)                                             │
└─────────────────────────────────────────────────────────────────┘
```

### Architecture Principles

**1. Single Responsibility Principle (SRP)**
- Each class has one job and does it well
- Controllers handle HTTP requests only
- Services contain business logic
- Repositories handle data access

**2. Dependency Injection**
- Dependencies injected via constructor
- Easy to test and mock
- Laravel's service container manages dependencies

**3. Interface Segregation**
- Use interfaces for contracts
- Easy to swap implementations
- Better testability

**4. Don't Repeat Yourself (DRY)**
- Reusable code in services and traits
- Helper functions for common operations
- Shared validation rules

---

## 2. Folder Structure

```
taskhub/
├── app/
│   ├── Console/
│   │   ├── Commands/
│   │   │   ├── SendDailyDigestCommand.php
│   │   │   ├── MarkOverdueTasksCommand.php
│   │   │   └── CleanOldNotificationsCommand.php
│   │   └── Kernel.php
│   │
│   ├── Events/
│   │   ├── TaskCreated.php
│   │   ├── TaskAssigned.php
│   │   ├── TaskCompleted.php
│   │   ├── CommentAdded.php
│   │   └── ProjectDeadlineApproaching.php
│   │
│   ├── Exceptions/
│   │   ├── Handler.php
│   │   ├── UnauthorizedException.php
│   │   └── InvalidOperationException.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── V1/
│   │   │   │   │   ├── TaskController.php
│   │   │   │   │   ├── ProjectController.php
│   │   │   │   │   └── ...
│   │   │   │   └── AuthController.php
│   │   │   ├── Auth/
│   │   │   │   ├── LoginController.php
│   │   │   │   ├── RegisterController.php
│   │   │   │   └── VerificationController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── ProjectController.php
│   │   │   ├── TaskController.php
│   │   │   ├── CommentController.php
│   │   │   ├── AttachmentController.php
│   │   │   ├── OrganizationController.php
│   │   │   ├── InvitationController.php
│   │   │   └── NotificationController.php
│   │   │
│   │   ├── Middleware/
│   │   │   ├── EnsureUserBelongsToOrganization.php
│   │   │   ├── EnsureUserHasProjectAccess.php
│   │   │   ├── CheckTaskPermission.php
│   │   │   └── LogActivity.php
│   │   │
│   │   ├── Requests/
│   │   │   ├── Task/
│   │   │   │   ├── StoreTaskRequest.php
│   │   │   │   ├── UpdateTaskRequest.php
│   │   │   │   └── AssignTaskRequest.php
│   │   │   ├── Project/
│   │   │   │   ├── StoreProjectRequest.php
│   │   │   │   └── UpdateProjectRequest.php
│   │   │   └── Organization/
│   │   │       ├── StoreOrganizationRequest.php
│   │   │       └── InviteMemberRequest.php
│   │   │
│   │   └── Resources/
│   │       ├── TaskResource.php
│   │       ├── TaskCollection.php
│   │       ├── ProjectResource.php
│   │       ├── UserResource.php
│   │       └── CommentResource.php
│   │
│   ├── Jobs/
│   │   ├── SendTaskAssignedEmail.php
│   │   ├── SendDailyDigestEmail.php
│   │   ├── ProcessBulkTaskImport.php
│   │   ├── GenerateProjectReport.php
│   │   └── OptimizeUploadedImage.php
│   │
│   ├── Listeners/
│   │   ├── SendTaskAssignedNotification.php
│   │   ├── LogTaskCreation.php
│   │   ├── NotifyProjectManager.php
│   │   └── UpdateProjectStatistics.php
│   │
│   ├── Models/
│   │   ├── User.php
│   │   ├── Organization.php
│   │   ├── Project.php
│   │   ├── Task.php
│   │   ├── Comment.php
│   │   ├── Attachment.php
│   │   ├── Tag.php
│   │   ├── Notification.php
│   │   ├── ActivityLog.php
│   │   ├── Invitation.php
│   │   └── Traits/
│   │       ├── BelongsToOrganization.php
│   │       ├── HasActivityLog.php
│   │       └── Searchable.php
│   │
│   ├── Notifications/
│   │   ├── TaskAssignedNotification.php
│   │   ├── TaskDueNotification.php
│   │   ├── CommentAddedNotification.php
│   │   ├── MentionedInCommentNotification.php
│   │   └── InvitationSentNotification.php
│   │
│   ├── Observers/
│   │   ├── TaskObserver.php
│   │   ├── ProjectObserver.php
│   │   ├── CommentObserver.php
│   │   └── UserObserver.php
│   │
│   ├── Policies/
│   │   ├── TaskPolicy.php
│   │   ├── ProjectPolicy.php
│   │   ├── CommentPolicy.php
│   │   ├── OrganizationPolicy.php
│   │   └── AttachmentPolicy.php
│   │
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   ├── RouteServiceProvider.php
│   │   └── RepositoryServiceProvider.php
│   │
│   ├── Repositories/
│   │   ├── Contracts/
│   │   │   ├── TaskRepositoryInterface.php
│   │   │   ├── ProjectRepositoryInterface.php
│   │   │   └── UserRepositoryInterface.php
│   │   ├── TaskRepository.php
│   │   ├── ProjectRepository.php
│   │   ├── UserRepository.php
│   │   └── BaseRepository.php
│   │
│   ├── Services/
│   │   ├── TaskService.php
│   │   ├── ProjectService.php
│   │   ├── OrganizationService.php
│   │   ├── InvitationService.php
│   │   ├── NotificationService.php
│   │   ├── FileUploadService.php
│   │   ├── ActivityLogService.php
│   │   └── ReportService.php
│   │
│   └── Helpers/
│       ├── helpers.php
│       └── constants.php
│
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   │   ├── UserFactory.php
│   │   ├── OrganizationFactory.php
│   │   ├── ProjectFactory.php
│   │   └── TaskFactory.php
│   ├── migrations/
│   └── seeders/
│
├── public/
├── resources/
│   ├── js/
│   │   ├── Components/
│   │   │   ├── Tasks/
│   │   │   │   ├── TaskList.vue
│   │   │   │   ├── TaskCard.vue
│   │   │   │   ├── TaskForm.vue
│   │   │   │   └── TaskDetail.vue
│   │   │   ├── Projects/
│   │   │   │   ├── ProjectList.vue
│   │   │   │   ├── ProjectCard.vue
│   │   │   │   └── ProjectDashboard.vue
│   │   │   ├── Common/
│   │   │   │   ├── Navbar.vue
│   │   │   │   ├── Sidebar.vue
│   │   │   │   ├── Modal.vue
│   │   │   │   └── Notification.vue
│   │   │   └── ...
│   │   ├── Pages/
│   │   │   ├── Auth/
│   │   │   │   ├── Login.vue
│   │   │   │   ├── Register.vue
│   │   │   │   └── ForgotPassword.vue
│   │   │   ├── Dashboard.vue
│   │   │   ├── Projects/
│   │   │   │   ├── Index.vue
│   │   │   │   ├── Show.vue
│   │   │   │   └── Create.vue
│   │   │   └── Tasks/
│   │   │       ├── Index.vue
│   │   │       └── Show.vue
│   │   ├── Composables/
│   │   │   ├── useAuth.js
│   │   │   ├── useTasks.js
│   │   │   └── useNotifications.js
│   │   └── app.js
│   │
│   ├── css/
│   │   └── app.css
│   │
│   └── views/
│       └── app.blade.php
│
├── routes/
│   ├── api.php
│   ├── web.php
│   └── channels.php
│
├── storage/
├── tests/
│   ├── Feature/
│   │   ├── Task/
│   │   │   ├── CreateTaskTest.php
│   │   │   ├── UpdateTaskTest.php
│   │   │   ├── DeleteTaskTest.php
│   │   │   └── AssignTaskTest.php
│   │   ├── Project/
│   │   │   └── ...
│   │   └── Auth/
│   │       └── ...
│   └── Unit/
│       ├── Services/
│       │   ├── TaskServiceTest.php
│       │   └── ProjectServiceTest.php
│       └── Models/
│           ├── TaskTest.php
│           └── ProjectTest.php
│
├── .env
├── .env.example
├── composer.json
├── package.json
├── phpunit.xml
├── docker-compose.yml
└── README.md
```

---

## 3. Design Patterns Implementation

### Pattern 1: Repository Pattern

**Purpose:** Separate data access logic from business logic

**Implementation:**

#### Step 1: Create Interface

```php
<?php

namespace App\Repositories\Contracts;

interface TaskRepositoryInterface
{
    public function find(int $id);
    public function all();
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function findByProject(int $projectId);
    public function findByAssignee(int $userId);
    public function search(array $filters);
}
```

#### Step 2: Implement Repository

```php
<?php

namespace App\Repositories;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository implements TaskRepositoryInterface
{
    protected $model;

    public function __construct(Task $model)
    {
        $this->model = $model;
    }

    public function find(int $id)
    {
        return $this->model->with(['assignee', 'creator', 'project', 'tags'])
            ->findOrFail($id);
    }

    public function all(): Collection
    {
        return $this->model->with(['assignee', 'project'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $task = $this->find($id);
        $task->update($data);
        return $task->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }

    public function findByProject(int $projectId): Collection
    {
        return $this->model->where('project_id', $projectId)
            ->with(['assignee', 'tags'])
            ->orderBy('position')
            ->get();
    }

    public function findByAssignee(int $userId): Collection
    {
        return $this->model->where('assigned_to', $userId)
            ->with(['project', 'tags'])
            ->whereNull('deleted_at')
            ->orderBy('due_date', 'asc')
            ->get();
    }

    public function search(array $filters): Collection
    {
        $query = $this->model->query();

        if (isset($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (isset($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (isset($filters['tags'])) {
            $query->whereHas('tags', function($q) use ($filters) {
                $q->whereIn('tags.id', $filters['tags']);
            });
        }

        if (isset($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                  ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        return $query->with(['assignee', 'tags', 'project'])
            ->orderBy('priority', 'desc')
            ->orderBy('due_date', 'asc')
            ->get();
    }
}
```

#### Step 3: Bind Interface to Implementation

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Repositories\TaskRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TaskRepositoryInterface::class,
            TaskRepository::class
        );

        // Bind other repositories
        $this->app->bind(
            ProjectRepositoryInterface::class,
            ProjectRepository::class
        );
    }
}
```

---

### Pattern 2: Service Layer Pattern

**Purpose:** Encapsulate business logic

**Implementation:**

```php
<?php

namespace App\Services;

use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Events\TaskCreated;
use App\Events\TaskAssigned;
use App\Exceptions\InvalidOperationException;
use Illuminate\Support\Facades\DB;

class TaskService
{
    protected $taskRepository;
    protected $activityLogService;
    protected $notificationService;

    public function __construct(
        TaskRepositoryInterface $taskRepository,
        ActivityLogService $activityLogService,
        NotificationService $notificationService
    ) {
        $this->taskRepository = $taskRepository;
        $this->activityLogService = $activityLogService;
        $this->notificationService = $notificationService;
    }

    public function createTask(array $data, int $userId): Task
    {
        return DB::transaction(function () use ($data, $userId) {
            // Set creator
            $data['created_by'] = $userId;

            // Create task
            $task = $this->taskRepository->create($data);

            // Attach tags if provided
            if (isset($data['tags'])) {
                $task->tags()->attach($data['tags']);
            }

            // Log activity
            $this->activityLogService->log(
                $task,
                'created',
                "Task created: {$task->title}",
                null,
                $task->toArray()
            );

            // Fire event (listeners will handle notifications)
            event(new TaskCreated($task));

            // If assigned, fire assignment event
            if ($task->assigned_to) {
                event(new TaskAssigned($task));
            }

            return $task->fresh(['assignee', 'tags', 'project']);
        });
    }

    public function updateTask(int $taskId, array $data, int $userId): Task
    {
        return DB::transaction(function () use ($taskId, $data, $userId) {
            $task = $this->taskRepository->find($taskId);
            $oldData = $task->toArray();

            // Check if assignee changed
            $assigneeChanged = isset($data['assigned_to']) 
                && $data['assigned_to'] != $task->assigned_to;

            // Update task
            $task = $this->taskRepository->update($taskId, $data);

            // Update tags if provided
            if (isset($data['tags'])) {
                $task->tags()->sync($data['tags']);
            }

            // Log activity
            $this->activityLogService->log(
                $task,
                'updated',
                "Task updated: {$task->title}",
                $oldData,
                $task->toArray()
            );

            // If assignee changed, fire event
            if ($assigneeChanged) {
                event(new TaskAssigned($task));
            }

            return $task->fresh(['assignee', 'tags', 'project']);
        });
    }

    public function changeStatus(int $taskId, string $newStatus, int $userId): Task
    {
        $task = $this->taskRepository->find($taskId);
        $oldStatus = $task->status;

        // Validate status transition
        $this->validateStatusTransition($oldStatus, $newStatus);

        // Update status
        $data = ['status' => $newStatus];
        
        // If marking as completed, set completed_at
        if ($newStatus === 'completed') {
            $data['completed_at'] = now();
        }

        $task = $this->taskRepository->update($taskId, $data);

        // Log activity
        $this->activityLogService->log(
            $task,
            'status_changed',
            "Status changed from {$oldStatus} to {$newStatus}",
            ['status' => $oldStatus],
            ['status' => $newStatus]
        );

        // Fire event if completed
        if ($newStatus === 'completed') {
            event(new TaskCompleted($task));
        }

        return $task;
    }

    public function assignTask(int $taskId, int $assigneeId, int $userId): Task
    {
        $task = $this->taskRepository->find($taskId);
        $oldAssignee = $task->assigned_to;

        // Update assignee
        $task = $this->taskRepository->update($taskId, [
            'assigned_to' => $assigneeId
        ]);

        // Log activity
        $this->activityLogService->log(
            $task,
            'reassigned',
            "Task reassigned",
            ['assigned_to' => $oldAssignee],
            ['assigned_to' => $assigneeId]
        );

        // Fire event
        event(new TaskAssigned($task));

        return $task;
    }

    public function deleteTask(int $taskId, int $userId): bool
    {
        $task = $this->taskRepository->find($taskId);

        // Log before deletion
        $this->activityLogService->log(
            $task,
            'deleted',
            "Task deleted: {$task->title}",
            $task->toArray(),
            null
        );

        return $this->taskRepository->delete($taskId);
    }

    protected function validateStatusTransition(string $oldStatus, string $newStatus): void
    {
        $validTransitions = [
            'to_do' => ['in_progress', 'on_hold'],
            'in_progress' => ['in_review', 'on_hold', 'to_do'],
            'in_review' => ['completed', 'in_progress'],
            'completed' => ['in_progress'], // Reopening
            'on_hold' => ['in_progress', 'to_do'],
        ];

        if (!isset($validTransitions[$oldStatus]) 
            || !in_array($newStatus, $validTransitions[$oldStatus])) {
            throw new InvalidOperationException(
                "Cannot transition from {$oldStatus} to {$newStatus}"
            );
        }
    }
}
```

---

### Pattern 3: Observer Pattern

**Purpose:** React to model events automatically

**Implementation:**

```php
<?php

namespace App\Observers;

use App\Models\Task;
use App\Services\ActivityLogService;

class TaskObserver
{
    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function created(Task $task): void
    {
        // Auto-log task creation
        $this->activityLogService->log(
            $task,
            'created',
            auth()->user()->name . " created this task",
            null,
            $task->toArray()
        );
    }

    public function updated(Task $task): void
    {
        // Log what changed
        $changes = $task->getChanges();
        $original = $task->getOriginal();

        if (!empty($changes)) {
            $this->activityLogService->log(
                $task,
                'updated',
                "Task updated",
                $original,
                $changes
            );
        }
    }

    public function deleting(Task $task): void
    {
        // Cascade delete comments and attachments
        $task->comments()->delete();
        $task->attachments()->delete();
        $task->tags()->detach();
    }

    public function deleted(Task $task): void
    {
        // Log deletion
        $this->activityLogService->log(
            $task,
            'deleted',
            auth()->user()->name . " deleted this task",
            $task->toArray(),
            null
        );
    }
}
```

**Register Observer:**

```php
<?php

namespace App\Providers;

use App\Models\Task;
use App\Observers\TaskObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Task::observe(TaskObserver::class);
        Project::observe(ProjectObserver::class);
        Comment::observe(CommentObserver::class);
    }
}
```

---

### Pattern 4: Strategy Pattern

**Purpose:** Different algorithms for same operation

**Implementation:** Notification Strategies

```php
<?php

namespace App\Services\Notifications;

interface NotificationStrategyInterface
{
    public function send($user, $data): void;
}
```

```php
<?php

namespace App\Services\Notifications;

use App\Notifications\TaskAssignedNotification;

class EmailNotificationStrategy implements NotificationStrategyInterface
{
    public function send($user, $data): void
    {
        $user->notify(new TaskAssignedNotification($data['task']));
    }
}
```

```php
<?php

namespace App\Services\Notifications;

use App\Models\Notification;

class DatabaseNotificationStrategy implements NotificationStrategyInterface
{
    public function send($user, $data): void
    {
        Notification::create([
            'id' => (string) \Str::uuid(),
            'type' => 'App\Notifications\TaskAssignedNotification',
            'notifiable_type' => get_class($user),
            'notifiable_id' => $user->id,
            'data' => $data,
        ]);
    }
}
```

```php
<?php

namespace App\Services;

class NotificationService
{
    protected $strategies = [];

    public function addStrategy(string $channel, $strategy): void
    {
        $this->strategies[$channel] = $strategy;
    }

    public function notify($user, array $data, array $channels = ['database', 'email']): void
    {
        foreach ($channels as $channel) {
            if (isset($this->strategies[$channel])) {
                $this->strategies[$channel]->send($user, $data);
            }
        }
    }
}
```

---

### Pattern 5: Factory Pattern

**Purpose:** Create objects without specifying exact class

**Implementation:**

```php
<?php

namespace App\Factories;

use App\Notifications\TaskAssignedNotification;
use App\Notifications\TaskDueNotification;
use App\Notifications\CommentAddedNotification;
use App\Notifications\MentionedInCommentNotification;

class NotificationFactory
{
    public static function create(string $type, $data)
    {
        return match($type) {
            'task_assigned' => new TaskAssignedNotification($data),
            'task_due' => new TaskDueNotification($data),
            'comment_added' => new CommentAddedNotification($data),
            'mentioned' => new MentionedInCommentNotification($data),
            default => throw new \InvalidArgumentException("Unknown notification type: {$type}"),
        };
    }
}
```

**Usage:**

```php
$notification = NotificationFactory::create('task_assigned', $task);
$user->notify($notification);
```

---

## 4. Laravel Best Practices

### 1. Request Validation

**Use Form Requests for validation:**

```php
<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', [Task::class, $this->project]);
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:500',
            'description' => 'nullable|string|max:10000',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:to_do,in_progress,in_review,completed,on_hold',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date|after:now',
            'estimated_hours' => 'nullable|numeric|min:0.5|max:999.9',
            'parent_task_id' => 'nullable|exists:tasks,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Task title is required',
            'title.max' => 'Task title cannot exceed 500 characters',
            'due_date.after' => 'Due date must be in the future',
        ];
    }
}
```

### 2. Authorization with Policies

```php
<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Task;

class TaskPolicy
{
    public function viewAny(User $user, $project): bool
    {
        return $user->isProjectMember($project);
    }

    public function view(User $user, Task $task): bool
    {
        return $user->isProjectMember($task->project);
    }

    public function create(User $user, $project): bool
    {
        return $user->isProjectMember($project);
    }

    public function update(User $user, Task $task): bool
    {
        return $user->isProjectManager($task->project) 
            || $task->assigned_to === $user->id;
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->isProjectManager($task->project);
    }

    public function assign(User $user, Task $task): bool
    {
        return $user->isProjectManager($task->project);
    }
}
```

### 3. Eloquent Scopes

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    // Local Scopes
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->where('status', '!=', 'completed');
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', now()->toDateString());
    }

    public function scopeDueThisWeek($query)
    {
        return $query->whereBetween('due_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    // Usage Example:
    // Task::inProgress()->highPriority()->assignedTo($userId)->get();
}
```

### 4. Model Traits for Reusability

```php
<?php

namespace App\Models\Traits;

use App\Models\Organization;

trait BelongsToOrganization
{
    /**
     * Boot the trait
     */
    protected static function bootBelongsToOrganization()
    {
        // Automatically set organization_id when creating
        static::creating(function ($model) {
            if (!$model->organization_id && auth()->check()) {
                $model->organization_id = auth()->user()->current_organization_id;
            }
        });

        // Global scope to filter by organization
        static::addGlobalScope('organization', function ($query) {
            if (auth()->check() && auth()->user()->current_organization_id) {
                $query->where('organization_id', auth()->user()->current_organization_id);
            }
        });
    }

    /**
     * Organization relationship
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
```

```php
<?php

namespace App\Models\Traits;

use App\Models\ActivityLog;

trait HasActivityLog
{
    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'loggable')
            ->orderBy('created_at', 'desc');
    }

    public function logActivity(string $action, string $description, ?array $oldValues = null, ?array $newValues = null)
    {
        return $this->activityLogs()->create([
            'organization_id' => $this->organization_id,
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
```

**Usage in Model:**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToOrganization;
use App\Models\Traits\HasActivityLog;

class Task extends Model
{
    use BelongsToOrganization, HasActivityLog;

    // ... rest of model
}
```

### 5. API Resources for Data Transformation

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => $this->status,
            'due_date' => $this->due_date?->format('Y-m-d H:i:s'),
            'estimated_hours' => $this->estimated_hours,
            'actual_hours' => $this->actual_hours,
            'is_overdue' => $this->due_date && $this->due_date->isPast() && $this->status !== 'completed',
            'completion_percentage' => $this->getCompletionPercentage(),
            
            // Relationships
            'assignee' => new UserResource($this->whenLoaded('assignee')),
            'creator' => new UserResource($this->whenLoaded('creator')),
            'project' => new ProjectResource($this->whenLoaded('project')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'comments_count' => $this->when($this->comments_count !== null, $this->comments_count),
            'attachments_count' => $this->when($this->attachments_count !== null, $this->attachments_count),
            
            // Permissions
            'can' => [
                'update' => $request->user()->can('update', $this->resource),
                'delete' => $request->user()->can('delete', $this->resource),
                'assign' => $request->user()->can('assign', $this->resource),
            ],
            
            // Timestamps
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
```

### 6. Events and Listeners

```php
<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskAssigned
{
    use Dispatchable, SerializesModels;

    public $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }
}
```

```php
<?php

namespace App\Listeners;

use App\Events\TaskAssigned;
use App\Notifications\TaskAssignedNotification;

class SendTaskAssignedNotification
{
    public function handle(TaskAssigned $event): void
    {
        $task = $event->task;

        // Send notification to assignee
        if ($task->assignee) {
            $task->assignee->notify(new TaskAssignedNotification($task));
        }

        // Notify project managers
        $managers = $task->project->members()
            ->wherePivot('role', 'manager')
            ->get();

        foreach ($managers as $manager) {
            $manager->notify(new TaskAssignedNotification($task));
        }
    }
}
```

**Register in EventServiceProvider:**

```php
<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TaskCreated::class => [
            LogTaskCreation::class,
            SendTaskCreatedNotification::class,
        ],
        TaskAssigned::class => [
            SendTaskAssignedNotification::class,
        ],
        TaskCompleted::class => [
            NotifyProjectManager::class,
            UpdateProjectStatistics::class,
        ],
        CommentAdded::class => [
            NotifyMentionedUsers::class,
            NotifyTaskAssignee::class,
        ],
    ];
}
```

### 7. Jobs for Background Processing

```php
<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Task;
use App\Mail\DailyDigestMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendDailyDigestEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle(): void
    {
        // Get tasks due today
        $tasksDueToday = Task::where('assigned_to', $this->user->id)
            ->whereDate('due_date', now()->toDateString())
            ->where('status', '!=', 'completed')
            ->with(['project', 'tags'])
            ->get();

        // Get overdue tasks
        $overdueTasks = Task::where('assigned_to', $this->user->id)
            ->where('due_date', '<', now())
            ->where('status', '!=', 'completed')
            ->with(['project', 'tags'])
            ->get();

        // Get completed tasks yesterday
        $completedYesterday = Task::where('assigned_to', $this->user->id)
            ->whereDate('completed_at', now()->subDay()->toDateString())
            ->with(['project'])
            ->get();

        // Only send if there's something to report
        if ($tasksDueToday->isEmpty() && $overdueTasks->isEmpty() && $completedYesterday->isEmpty()) {
            return;
        }

        Mail::to($this->user->email)->send(
            new DailyDigestMail($this->user, $tasksDueToday, $overdueTasks, $completedYesterday)
        );
    }

    public function failed(\Throwable $exception): void
    {
        // Log failure
        \Log::error('Failed to send daily digest', [
            'user_id' => $this->user->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
```

**Dispatch Job:**

```php
// In a command or controller
SendDailyDigestEmail::dispatch($user);

// Dispatch with delay
SendDailyDigestEmail::dispatch($user)->delay(now()->addMinutes(10));

// Dispatch to specific queue
SendDailyDigestEmail::dispatch($user)->onQueue('emails');
```

### 8. Console Commands

```php
<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Jobs\SendDailyDigestEmail;
use App\Models\User;
use Illuminate\Console\Command;

class SendDailyDigestCommand extends Command
{
    protected $signature = 'digest:send {--user_id=}';
    protected $description = 'Send daily digest emails to all active users';

    public function handle()
    {
        $this->info('Starting daily digest...');

        $query = User::whereHas('tasks', function($q) {
            $q->where('due_date', '>=', now()->toDateString());
        });

        // Filter by specific user if provided
        if ($userId = $this->option('user_id')) {
            $query->where('id', $userId);
        }

        $users = $query->get();
        $bar = $this->output->createProgressBar($users->count());

        foreach ($users as $user) {
            SendDailyDigestEmail::dispatch($user);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Daily digest emails queued successfully!');
    }
}
```

**Schedule Commands:**

```php
<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Send daily digest at 8 AM
        $schedule->command('digest:send')
            ->dailyAt('08:00')
            ->timezone('UTC');

        // Mark overdue tasks every hour
        $schedule->command('tasks:mark-overdue')
            ->hourly();

        // Clean old notifications daily
        $schedule->command('notifications:clean')
            ->dailyAt('02:00');

        // Generate weekly reports every Monday
        $schedule->command('reports:generate-weekly')
            ->weeklyOn(1, '09:00');

        // Database backup daily
        $schedule->command('backup:run')
            ->dailyAt('03:00');
    }
}
```

---

## 5. Code Examples

### Complete Controller Example

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of tasks
     */
    public function index(Request $request, Project $project): Response
    {
        $this->authorize('viewAny', [Task::class, $project]);

        $filters = $request->only(['status', 'priority', 'assigned_to', 'tags', 'search']);
        $filters['project_id'] = $project->id;

        $tasks = $this->taskService->getTasks($filters);

        return Inertia::render('Tasks/Index', [
            'project' => $project,
            'tasks' => TaskResource::collection($tasks),
            'filters' => $filters,
        ]);
    }

    /**
     * Show task details
     */
    public function show(Task $task): Response
    {
        $this->authorize('view', $task);

        $task->load([
            'assignee',
            'creator',
            'project',
            'tags',
            'comments.user',
            'attachments',
            'activityLogs.user',
            'subtasks'
        ]);

        return Inertia::render('Tasks/Show', [
            'task' => new TaskResource($task),
        ]);
    }

    /**
     * Store a new task
     */
    public function store(StoreTaskRequest $request, Project $project): JsonResponse
    {
        $this->authorize('create', [Task::class, $project]);

        $task = $this->taskService->createTask(
            array_merge($request->validated(), ['project_id' => $project->id]),
            auth()->id()
        );

        return response()->json([
            'message' => 'Task created successfully',
            'task' => new TaskResource($task),
        ], 201);
    }

    /**
     * Update task
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $task = $this->taskService->updateTask(
            $task->id,
            $request->validated(),
            auth()->id()
        );

        return response()->json([
            'message' => 'Task updated successfully',
            'task' => new TaskResource($task),
        ]);
    }

    /**
     * Delete task
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $this->taskService->deleteTask($task->id, auth()->id());

        return response()->json([
            'message' => 'Task deleted successfully',
        ]);
    }

    /**
     * Change task status
     */
    public function updateStatus(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $request->validate([
            'status' => 'required|in:to_do,in_progress,in_review,completed,on_hold',
        ]);

        $task = $this->taskService->changeStatus(
            $task->id,
            $request->status,
            auth()->id()
        );

        return response()->json([
            'message' => 'Status updated successfully',
            'task' => new TaskResource($task),
        ]);
    }

    /**
     * Assign task to user
     */
    public function assign(Request $request, Task $task): JsonResponse
    {
        $this->authorize('assign', $task);

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $task = $this->taskService->assignTask(
            $task->id,
            $request->user_id,
            auth()->id()
        );

        return response()->json([
            'message' => 'Task assigned successfully',
            'task' => new TaskResource($task),
        ]);
    }
}
```

### Complete Model Example

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\BelongsToOrganization;
use App\Models\Traits\HasActivityLog;

class Task extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization, HasActivityLog;

    protected $fillable = [
        'organization_id',
        'project_id',
        'parent_task_id',
        'title',
        'description',
        'priority',
        'status',
        'assigned_to',
        'created_by',
        'due_date',
        'estimated_hours',
        'actual_hours',
        'completed_at',
        'position',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'position' => 'integer',
    ];

    protected $with = ['assignee:id,name,email,avatar'];

    protected $appends = ['is_overdue', 'status_label'];

    // Relationships

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'task_tag')
            ->withTimestamps();
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    // Scopes

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->where('status', '!=', 'completed');
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', now()->toDateString());
    }

    public function scopeDueThisWeek($query)
    {
        return $query->whereBetween('due_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    // Accessors & Mutators

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date 
            && $this->due_date->isPast() 
            && $this->status !== 'completed';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'to_do' => 'To Do',
            'in_progress' => 'In Progress',
            'in_review' => 'In Review',
            'completed' => 'Completed',
            'on_hold' => 'On Hold',
            default => $this->status,
        };
    }

    // Methods

    public function getCompletionPercentage(): int
    {
        if ($this->subtasks()->count() === 0) {
            return $this->status === 'completed' ? 100 : 0;
        }

        $completed = $this->subtasks()->where('status', 'completed')->count();
        $total = $this->subtasks()->count();

        return round(($completed / $total) * 100);
    }

    public function isAssignedTo(User $user): bool
    {
        return $this->assigned_to === $user->id;
    }

    public function canBeDeletedBy(User $user): bool
    {
        return $user->isProjectManager($this->project) 
            || $this->created_by === $user->id;
    }
}
```

---

## 6. Testing Strategy

### Feature Test Example

```php
<?php

namespace Tests\Feature\Task;

use App\Models\User;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTaskTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $organization;
    protected $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create();
        
        // Attach user to organization
        $this->organization->members()->attach($this->user->id, [
            'role' => 'manager'
        ]);

        $this->project = Project::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        // Add user to project
        $this->project->members()->attach($this->user->id, [
            'role' => 'member'
        ]);
    }

    /** @test */
    public function user_can_create_task_in_project()
    {
        $this->actingAs($this->user);

        $taskData = [
            'title' => 'New Task',
            'description' => 'Task description',
            'priority' => 'high',
            'status' => 'to_do',
            'due_date' => now()->addDays(7)->format('Y-m-d H:i:s'),
        ];

        $response = $this->postJson(
            route('projects.tasks.store', $this->project),
            $taskData
        );

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Task created successfully',
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'New Task',
            'project_id' => $this->project->id,
            'created_by' => $this->user->id,
        ]);
    }

    /** @test */
    public function task_title_is_required()
    {
        $this->actingAs($this->user);

        $response = $this->postJson(
            route('projects.tasks.store', $this->project),
            [
                'description' => 'Task without title',
            ]
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    /** @test */
    public function user_cannot_create_task_in_project_they_are_not_member_of()
    {
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser);

        $response = $this->postJson(
            route('projects.tasks.store', $this->project),
            [
                'title' => 'Unauthorized Task',
            ]
        );

        $response->assertStatus(403);
    }

    /** @test */
    public function creating_task_fires_event()
    {
        Event::fake([TaskCreated::class]);

        $this->actingAs($this->user);

        $this->postJson(
            route('projects.tasks.store', $this->project),
            [
                'title' => 'New Task',
                'priority' => 'medium',
            ]
        );

        Event::assertDispatched(TaskCreated::class);
    }

    /** @test */
    public function creating_task_logs_activity()
    {
        $this->actingAs($this->user);

        $this->postJson(
            route('projects.tasks.store', $this->project),
            [
                'title' => 'New Task',
            ]
        );

        $task = Task::where('title', 'New Task')->first();

        $this->assertDatabaseHas('activity_logs', [
            'loggable_type' => Task::class,
            'loggable_id' => $task->id,
            'action' => 'created',
            'user_id' => $this->user->id,
        ]);
    }
}
```

### Unit Test Example

```php
<?php

namespace Tests\Unit\Services;

use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class TaskServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $taskService;
    protected $taskRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->taskRepository = Mockery::mock(TaskRepositoryInterface::class);
        $this->taskService = new TaskService(
            $this->taskRepository,
            app(ActivityLogService::class),
            app(NotificationService::class)
        );
    }

    /** @test */
    public function it_can_create_task()
    {
        $user = User::factory()->create();
        $taskData = [
            'title' => 'Test Task',
            'project_id' => 1,
            'priority' => 'high',
        ];

        $this->taskRepository
            ->shouldReceive('create')
            ->once()
            ->andReturn(Task::factory()->make($taskData));

        $task = $this->taskService->createTask($taskData, $user->id);

        $this->assertEquals('Test Task', $task->title);
    }

    /** @test */
    public function it_validates_status_transition()
    {
        $this->expectException(InvalidOperationException::class);

        $task = Task::factory()->create(['status' => 'to_do']);

        $this->taskRepository
            ->shouldReceive('find')
            ->once()
            ->andReturn($task);

        // Invalid transition: to_do -> completed (must go through in_progress)
        $this->taskService->changeStatus($task->id, 'completed', 1);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
```

---

## Summary

This architecture documentation provides:

✅ **Clear separation of concerns** with layered architecture
✅ **Design patterns** for maintainable code
✅ **Best practices** for Laravel development
✅ **Complete code examples** for reference
✅ **Testing strategy** for quality assurance

### Key Takeaways:

1. **Controllers** should be thin - delegate to services
2. **Services** contain business logic
3. **Repositories** handle data access
4. **Models** define relationships and scopes
5. **Observers** handle automatic side effects
6. **Events/Listeners** for decoupled notifications
7. **Jobs** for background processing
8. **Policies** for authorization
9. **Form Requests** for validation
10. **Resources** for API responses

---

**You now have complete documentation for building TaskHub!** 🎉

This covers:
1. ✅ System Flow & Process Documentation
2. ✅ Database Design Documentation  
3. ✅ Architecture & Design Patterns Documentation

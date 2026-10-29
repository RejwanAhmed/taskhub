<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskTypeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->middleware('guest');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('organizations', OrganizationController::class)->except(['create', 'show', 'edit']);
    Route::prefix('organizations')->group(function () {
        Route::post('{organization}/switch', [OrganizationController::class, 'switchOrganization'])->name('organizations.switch');
    });

    Route::middleware(['org.access'])->group(function () {
        Route::get('members', [OrganizationController::class, 'members'])->name('members');

        Route::prefix('invitations')->name('invitations.')->group(function () {
            Route::post('/', [InvitationController::class, 'store'])->name('store');
        });        
        
        Route::resource('task-types', TaskTypeController::class)->except(['create', 'edit', 'show']);
        Route::resource('tags', TagController::class)->except(['create', 'edit', 'show']);

        Route::resource('projects', ProjectController::class)->except(['create', 'edit', 'destroy']);
        Route::prefix('projects/{project}')->group(function () {
            Route::put('assign-members', [ProjectController::class, 'assignMembers'])->name('projects.assignMembers');
            Route::get('tasks', [TaskController::class, 'index'])->name('projects.tasks.index');
            Route::post('tasks', [TaskController::class, 'store'])->name('projects.tasks.store');
            Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('projects.tasks.destroy');
            Route::put('tasks/{task}', [TaskController::class, 'update'])->name('projects.tasks.update');
            Route::get('tasks/{task}/subtasks', [TaskController::class, 'getSubtasks'])->name('projects.tasks.subtasks.index');
            Route::post('tasks/{task}/subtasks', [TaskController::class, 'storeSubtask'])->name('projects.tasks.subtasks.store');
            Route::put('/tasks/{task}/subtasks/{subtask}', [TaskController::class, 'updateSubtask'])->name('projects.tasks.subtasks.update');
            Route::delete('tasks/{task}/subtasks/{subtask}', [TaskController::class, 'destroySubtask'])->name('projects.tasks.subtasks.destroy');
            Route::post('tasks/reorder', [TaskController::class, 'reorder'])->name('projects.tasks.reorder');
        });
        Route::prefix('tasks/{task}')->group(function () {
            Route::get('comments', [CommentController::class, 'index'])->name('tasks.comments.index');
            Route::post('comments', [CommentController::class, 'store'])->name('tasks.comments.store');
            Route::put('comments/{comment}', [CommentController::class, 'update'])->name('tasks.comments.update');
            Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('tasks.comments.destroy');

            Route::get('attachments', [AttachmentController::class, 'index'])->name('tasks.attachments.index');
            Route::post('attachments', [AttachmentController::class, 'store'])->name('tasks.attachments.store');
            Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('tasks.attachments.destroy');
        });
    });
});

Route::prefix('invitations')->name('invitations.')->group(function() {
    Route::get('accept/{token}', [InvitationController::class, 'show'])->middleware('signed')->name('show');
    Route::post('accept/{token}', [InvitationController::class, 'accept'])->name('accept');
    Route::get('register/{token}', [InvitationController::class, 'register'])->name('register');
    Route::post('register/{token}', [InvitationController::class, 'storeRegistration'])->name('storeRegistration');
    Route::get('login/{token}', [InvitationController::class, 'login'])->name('login');
    Route::post('login/{token}', [InvitationController::class, 'storeLogin'])->name('storeLogin');
});

require __DIR__.'/auth.php';

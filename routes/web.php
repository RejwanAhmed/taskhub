<?php

use App\Http\Controllers\InvitationController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ProfileController;
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
});

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
        Route::get('members', [OrganizationController::class, 'members'])->name('organizations.member');
    });

    Route::prefix('invitations')->group(function () {
        Route::post('/', [InvitationController::class, 'store'])->name('invitations.store');
    });
});

Route::prefix('invitations')->group(function () {
    Route::get('accept/{token}', [InvitationController::class, 'show'])->name('invitations.show');
});

require __DIR__.'/auth.php';

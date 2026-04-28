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
        Route::get('members', [OrganizationController::class, 'members'])->name('organizations.member');
    });

    Route::prefix('invitations')->name('invitations.')->group(function () {
        Route::post('/', [InvitationController::class, 'store'])->name('store');
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

<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\SocialiteController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

// socialite auth
Route::middleware('sso')->group(function () {
    Route::get('/auth/{provider}', [SocialiteController::class, 'redirectToProvider']);
    Route::get('/auth/{provider}/callback', [SocialiteController::class, 'handleProvideCallback']);
});

Route::middleware(['auth', 'verified'])->group(function () {

    // dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // permissions
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');

    // roles
    Route::resource('/roles', RoleController::class);

    // users
    Route::resource('/users', UserController::class);
    Route::patch('/users/{user}/restore', [UserController::class, 'restore'])
        ->name('users.restore')
        ->withTrashed();

    Route::delete('/users/{user}/force-delete', [UserController::class, 'forceDelete'])
        ->name('users.force-delete')
        ->withTrashed();
});

require __DIR__.'/settings.php';

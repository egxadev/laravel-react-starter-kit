<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PermissionController;

Route::get('/', function () {
    return inertia('welcome');
})->name('home');

// socialite auth
Route::get('/auth/{provider}', [SocialiteController::class, 'redirectToProvider']);
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'handleProvideCallback']);

Route::middleware(['auth', 'verified'])->group(function () {

    // dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // permissions
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index')
        ->middleware('permission:permissions.index');

    // roles
    Route::resource('/roles', RoleController::class)
        ->middleware('permission:roles.index|roles.create|roles.edit|roles.delete');

    // users
    Route::resource('/users', UserController::class)
        ->middleware('permission:users.index|users.create|users.edit|users.delete');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;

Route::get('/', function () {
    return inertia('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    // dashboard
    Route::get('dashboard', function () {
        return inertia('dashboard');
    })->name('dashboard');

    // permissions
    Route::get('/permissions', PermissionController::class)->name('permissions.index')
        ->middleware('permission:permissions.index');

    // roles
    Route::resource('/roles', RoleController::class)
        ->middleware('permission:roles.index|roles.create|roles.edit|roles.delete');

});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

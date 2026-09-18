<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('roles.index');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('roles.create');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('roles.index');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('roles.edit');
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('roles.delete');
    }
}

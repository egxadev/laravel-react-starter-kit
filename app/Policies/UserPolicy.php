<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $actor): bool
    {
        return $actor->hasPermissionTo('users.index');
    }

    public function create(User $actor): bool
    {
        return $actor->hasPermissionTo('users.create');
    }

    public function view(User $actor, User $target): bool
    {
        return $actor->hasPermissionTo('users.index');
    }

    public function update(User $actor, User $target): bool
    {
        return $actor->hasPermissionTo('users.edit');
    }

    public function delete(User $actor, User $target): bool
    {
        return $actor->hasPermissionTo('users.delete')
            && (string) $actor->id !== (string) $target->id;
    }

    public function restore(User $actor, User $target): bool
    {
        return $actor->hasPermissionTo('users.delete');
    }

    public function forceDelete(User $actor, User $target): bool
    {
        return $actor->hasPermissionTo('users.delete')
            && (string) $actor->id !== (string) $target->id;
    }
}

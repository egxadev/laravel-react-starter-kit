<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Resolves a policy ability to the `<resource>.<action>` permission it needs.
 * Subclasses declare the resource segment once.
 */
abstract class ResourcePolicy
{
    /** Permission resource segment, e.g. "users". */
    protected string $resource;

    public function viewAny(User $user): bool
    {
        return $this->allows($user, 'index');
    }

    public function view(User $user, Model $model): bool
    {
        return $this->allows($user, 'index');
    }

    public function create(User $user): bool
    {
        return $this->allows($user, 'create');
    }

    public function update(User $user, Model $model): bool
    {
        return $this->allows($user, 'edit');
    }

    public function delete(User $user, Model $model): bool
    {
        return $this->allows($user, 'delete');
    }

    protected function allows(User $user, string $action): bool
    {
        $permission = Permission::tryFrom("{$this->resource}.{$action}");

        return $permission !== null && $user->hasPermissionTo($permission->value);
    }
}

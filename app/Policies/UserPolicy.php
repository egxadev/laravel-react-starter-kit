<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserPolicy extends ResourcePolicy
{
    protected string $resource = 'users';

    public function delete(User $actor, Model $target): bool
    {
        return parent::delete($actor, $target) && $this->isNotSelf($actor, $target);
    }

    public function restore(User $actor, Model $target): bool
    {
        return $this->allows($actor, 'delete');
    }

    public function forceDelete(User $actor, Model $target): bool
    {
        return parent::delete($actor, $target) && $this->isNotSelf($actor, $target);
    }

    private function isNotSelf(User $actor, Model $target): bool
    {
        return (string) $actor->id !== (string) $target->id;
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        return inertia('users/index', User::filterPaginate($request->all()));
    }

    public function create()
    {
        $this->authorize('create', User::class);

        return inertia('users/create', [
            'roles' => Role::all(),
        ]);
    }

    public function store(UserRequest $request)
    {
        $this->authorize('create', User::class);

        User::createWithRoles($request->validated(), $request->validated('roles'));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'User created successfully.']);

        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return inertia('users/edit', [
            'roles' => Role::all(),
            'user' => $user->load('roles'),
        ]);
    }

    public function update(UserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $user->updateWithRoles($request->validated(), $request->validated('roles'));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'User updated successfully.']);

        return redirect()->route('users.index');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'User deleted successfully.']);

        return redirect()->route('users.index');
    }

    public function restore(User $user)
    {
        $this->authorize('restore', $user);

        $user->restore();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'User restored successfully.']);

        return redirect()->route('users.index');
    }

    public function forceDelete(User $user)
    {
        $this->authorize('forceDelete', $user);

        $user->forceDelete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'User permanently deleted successfully.']);

        return redirect()->route('users.index');
    }
}

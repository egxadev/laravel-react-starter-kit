<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Role::class);

        return inertia('roles/index', Role::filterPaginate($request->all()));
    }

    public function create()
    {
        $this->authorize('create', Role::class);

        return inertia('roles/create', [
            'permissions' => Permission::all(),
        ]);
    }

    public function store(RoleRequest $request)
    {
        $this->authorize('create', Role::class);

        Role::createWithPermissions($request->validated(), $request->validated('permissions'));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Role created successfully.']);

        return redirect()->route('roles.index');
    }

    public function edit(Role $role)
    {
        $this->authorize('update', $role);

        return inertia('roles/edit', [
            'role' => $role->load('permissions'),
            'permissions' => Permission::all(),
        ]);
    }

    public function update(RoleRequest $request, Role $role)
    {
        $this->authorize('update', $role);

        $role->updateWithPermissions($request->validated(), $request->validated('permissions'));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Role updated successfully.']);

        return redirect()->route('roles.index');
    }

    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);

        $role->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Role deleted successfully.']);

        return redirect()->route('roles.index');
    }
}

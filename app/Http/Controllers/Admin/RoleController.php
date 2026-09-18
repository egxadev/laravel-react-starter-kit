<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;

class RoleController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Role::class);

        $breadcrumbs = [
            [
                'title' => 'Role',
                'href' => route('roles.index'),
            ],
        ];

        $data = Role::filterPaginate($request->all());

        return inertia('roles/index', array_merge(
            ['breadcrumbs' => $breadcrumbs],
            $data
        ));
    }

    public function create()
    {
        $this->authorize('create', Role::class);

        $breadcrumbs = [
            [
                'title' => 'Role',
                'href' => route('roles.index'),
            ],
            [
                'title' => 'Create',
                'href' => route('roles.create'),
            ],
        ];

        return inertia('roles/create', [
            'breadcrumbs' => $breadcrumbs,
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

        $breadcrumbs = [
            [
                'title' => 'Role',
                'href' => route('roles.index'),
            ],
            [
                'title' => 'Edit',
                'href' => route('roles.edit', $role),
            ],
        ];

        return inertia('roles/edit', [
            'breadcrumbs' => $breadcrumbs,
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

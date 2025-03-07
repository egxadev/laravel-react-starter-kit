<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $breadcrumbs = [
            [
                'title' => 'Role',
                'href' => '/roles'
            ]
        ];

        $roles = Role::all();

        return inertia('roles/index', [
            'breadcrumbs' => $breadcrumbs,
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $breadcrumbs = [
            [
                'title' => 'Role',
                'href' => '/roles'
            ],
            [
                'title' => 'Create',
                'href' => '/roles/create'
            ]
        ];

        $permissions = Permission::all();

        return inertia('roles/create', [
            'breadcrumbs' => $breadcrumbs,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /**
         * Validate request
         */
        $request->validate([
            'name'          => 'required|unique:roles,name',
            'permissions'   => 'required',
        ]);

        //create role
        $role = Role::create(['name' => $request->name]);

        //assign permissions to role
        $role->givePermissionTo($request->permissions);

        //redirect
        return redirect()->route('roles.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $breadcrumbs = [
            [
                'title' => 'Role',
                'href' => '/roles'
            ],
            [
                'title' => 'Create',
                'href' => '/roles/' . $id . '/edit'
            ]
        ];

        $role = Role::with('permissions')->findOrFail($id);

        $permissions = Permission::all();

        return inertia('roles/edit', [
            'breadcrumbs' => $breadcrumbs,
            'role'          => $role,
            'permissions'   => $permissions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name'          => 'required|unique:roles,name,' . $role->id,
            'permissions'   => 'required',
        ]);

        $role->update(['name' => $request->name]);

        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);

        $role->delete();

        return redirect()->route('roles.index');
    }
}

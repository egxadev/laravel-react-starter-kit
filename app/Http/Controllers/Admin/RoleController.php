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
        //get roles
        $roles = Role::all();

        //render with inertia
        return inertia('dashboard', [
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //get permission all
        $permissions = Permission::all();

        //render with inertia
        return inertia('dashboard', [
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
        //get role
        $role = Role::with('permissions')->findOrFail($id);

        //get permission all
        $permissions = Permission::all();

        //render with inertia
        return inertia('dashboard', [
            'role'          => $role,
            'permissions'   => $permissions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        /**
         * validate request
         */
        $request->validate([
            'name'          => 'required|unique:roles,name,' . $role->id,
            'permissions'   => 'required',
        ]);

        //update role
        $role->update(['name' => $request->name]);

        //sync permissions
        $role->syncPermissions($request->permissions);

        //redirect
        return redirect()->route('roles.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //find role by ID
        $role = Role::findOrFail($id);

        //delete role
        $role->delete();

        //redirect
        return redirect()->route('roles.index');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\RoleService;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $breadcrumbs = [
            [
                'title' => 'Role',
                'href' => route('roles.index')
            ]
        ];

        $data = $this->roleService->getPaginatedRoles($request->all());

        return inertia('roles/index', array_merge(
            ['breadcrumbs' => $breadcrumbs],
            $data
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $breadcrumbs = [
            [
                'title' => 'Role',
                'href' => route('roles.index')
            ],
            [
                'title' => 'Create',
                'href' => route('roles.create')
            ]
        ];

        return inertia('roles/create', [
            'breadcrumbs' => $breadcrumbs,
            'permissions' => Permission::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        $response = $this->roleService->createRole($request->all());

        if ($response['success']) {
            return redirect()->route('roles.index')->with('success', $response['message']);
        } else {
            return redirect()->route('roles.index')->with('error', $response['message']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $breadcrumbs = [
            [
                'title' => 'Role',
                'href' => route('roles.index')
            ],
            [
                'title' => 'Edit',
                'href' => route('roles.edit', $id)
            ]
        ];

        return inertia('roles/edit', [
            'breadcrumbs' => $breadcrumbs,
            'role'          => Role::with('permissions')->findOrFail($id),
            'permissions'   => Permission::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, Role $role)
    {
        $response = $this->roleService->updateRole($role, $request->all());

        if ($response['success']) {
            return redirect()->route('roles.index')->with('success', $response['message']);
        } else {
            return redirect()->route('roles.index')->with('error', $response['message']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $response = $this->roleService->deleteRole($id);

        if ($response['success']) {
            return redirect()->route('roles.index')->with('success', $response['message']);
        } else {
            return redirect()->route('roles.index')->with('error', $response['message']);
        }
    }
}

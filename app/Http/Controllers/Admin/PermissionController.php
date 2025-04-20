<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PermissionService;

class PermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Handle the incoming request.
     */
    public function index(Request $request)
    {
        $breadcrumbs = [
            [
                'title' => 'Permission',
                'href' => route('permissions.index')
            ]
        ];

        $data = $this->permissionService->getPaginatedPermissions($request->all());

        return inertia('permissions/index', array_merge(
            ['breadcrumbs' => $breadcrumbs],
            $data
        ));
    }
}

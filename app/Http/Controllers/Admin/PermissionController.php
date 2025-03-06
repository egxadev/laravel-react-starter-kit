<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $breadcrumbs = [
            [
                'title' => 'Permission',
                'href' => '/permissions'
            ]
        ];

        $permissions = Permission::all();

        return inertia('permissions/index', [
            'breadcrumbs' => $breadcrumbs,
            'permissions' => $permissions,
        ]);
    }
}

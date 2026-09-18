<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    use AuthorizesRequests;

    /**
     * Handle the incoming request.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Permission::class);

        $breadcrumbs = [
            [
                'title' => 'Permission',
                'href' => route('permissions.index'),
            ],
        ];

        $data = Permission::filterPaginate($request->all());

        return inertia('permissions/index', array_merge(
            ['breadcrumbs' => $breadcrumbs],
            $data
        ));
    }
}

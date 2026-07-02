<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $breadcrumbs = [
            [
                'title' => 'User',
                'href' => route('users.index'),
            ],
        ];

        $data = $this->userService->getPaginatedUsers($request->all());

        return inertia('users/index', array_merge(
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
                'title' => 'User',
                'href' => route('users.index'),
            ],
            [
                'title' => 'Create',
                'href' => route('users.create'),
            ],
        ];

        return inertia('users/create', [
            'breadcrumbs' => $breadcrumbs,
            'roles' => Role::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $response = $this->userService->createUser($request->validated());

        $type = $response['success'] ? 'success' : 'error';
        Inertia::flash('toast', ['type' => $type, 'message' => $response['message']]);

        return redirect()->route('users.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $breadcrumbs = [
            [
                'title' => 'User',
                'href' => route('users.index'),
            ],
            [
                'title' => 'Edit',
                'href' => route('users.edit', $id),
            ],
        ];

        return inertia('users/edit', [
            'breadcrumbs' => $breadcrumbs,
            'roles' => Role::all(),
            'user' => User::with('roles')->findOrFail($id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        $response = $this->userService->updateUser($user, $request->validated());

        $type = $response['success'] ? 'success' : 'error';
        Inertia::flash('toast', ['type' => $type, 'message' => $response['message']]);

        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $response = $this->userService->deleteUser($id);

        $type = $response['success'] ? 'success' : 'error';
        Inertia::flash('toast', ['type' => $type, 'message' => $response['message']]);

        $route = isset($response['redirect']) ? $response['redirect'] : 'users.index';

        return redirect()->route($route);
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id)
    {
        $response = $this->userService->restoreUser($id);

        $type = $response['success'] ? 'success' : 'error';
        Inertia::flash('toast', ['type' => $type, 'message' => $response['message']]);

        return redirect()->route('users.index');
    }

    /**
     * Force delete the specified resource from storage.
     */
    public function forceDelete(string $id)
    {
        $response = $this->userService->forceDeleteUser($id);

        $type = $response['success'] ? 'success' : 'error';
        Inertia::flash('toast', ['type' => $type, 'message' => $response['message']]);

        return redirect()->route('users.index');
    }
}

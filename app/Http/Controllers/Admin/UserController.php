<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;

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
                'href' => route('users.index')
            ]
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
                'href' => route('users.index')
            ],
            [
                'title' => 'Create',
                'href' => route('users.create')
            ]
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

        if ($response['success']) {
            return redirect()->route('users.index')->with('success', $response['message']);
        } else {
            return redirect()->route('users.index')->with('error', $response['message']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $breadcrumbs = [
            [
                'title' => 'User',
                'href' => route('users.index')
            ],
            [
                'title' => 'Edit',
                'href' => route('users.edit', $id)
            ]
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

        if ($response['success']) {
            return redirect()->route('users.index')->with('success', $response['message']);
        } else {
            return redirect()->route('users.index')->with('error', $response['message']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $response = $this->userService->deleteUser($id);

        if (isset($response['redirect'])) {
            return redirect()->route($response['redirect'])->with('success', $response['message']);
        }

        if ($response['success']) {
            return redirect()->route('users.index')->with('success', $response['message']);
        } else {
            return redirect()->route('users.index')->with('error', $response['message']);
        }
    }
}

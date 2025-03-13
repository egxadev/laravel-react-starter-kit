<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $breadcrumbs = [
            [
                'title' => 'User',
                'href' => '/users'
            ]
        ];

        $users = User::all();

        return inertia('users/index', [
            'breadcrumbs' => $breadcrumbs,
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $breadcrumbs = [
            [
                'title' => 'User',
                'href' => '/users'
            ],
            [
                'title' => 'Create',
                'href' => '/users/create'
            ]
        ];

        $roles = Role::all();

        return inertia('users/create', [
            'breadcrumbs'   => $breadcrumbs,
            'roles'         => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate( [
            'name'     => 'required',
            'email'    => 'required|unique:users',
            'password' => 'required|confirmed' 
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $user->assignRole($request->roles);

        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $breadcrumbs = [
            [
                'title' => 'User',
                'href' => '/users'
            ],
            [
                'title' => 'Edit',
                'href' => '/users/' . $id . '/edit'
            ]
        ];

        $user = User::with('roles')->findOrFail($id);

        $roles = Role::all();

        return inertia('users/edit', [
            'breadcrumbs'   => $breadcrumbs,
            'roles'         => $roles,
            'user'          => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|unique:users,email,'.$user->id,
            'password' => 'nullable|confirmed' 
        ]);

        if($request->password == '') {

            $user->update([
                'name'     => $request->name,
                'email'    => $request->email
            ]);

        } else {
                
            $user->update([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => bcrypt($request->password)
            ]);
            
        }

        $user->syncRoles($request->roles);

        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index');
    }
}

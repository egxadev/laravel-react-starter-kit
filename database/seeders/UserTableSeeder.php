<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name'      => 'Administrator',
            'email'     => 'admin@gmail.com',
            'password'  => bcrypt('password'),
        ]);

        $permissions = Permission::whereNotIn('name', function ($query) {
            $query->select('name')
                ->from('permissions')
                ->where('name', 'like', 'users.%');
        })->get();

        $role = Role::where('name', 'admin')->first();

        $role->syncPermissions($permissions);
        $user->assignRole($role);
    }
}

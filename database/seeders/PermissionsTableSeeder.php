<?php

namespace Database\Seeders;

use App\Enums\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission as PermissionModel;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Permission::cases() as $permission) {
            PermissionModel::create([
                'name' => $permission->value,
                'guard_name' => 'web',
            ]);
        }
    }
}

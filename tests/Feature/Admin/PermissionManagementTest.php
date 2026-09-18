<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionsTableSeeder;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionsTableSeeder::class);
        $this->seed(RolesTableSeeder::class);

        $role = Role::findByName('admin');
        $role->syncPermissions(Permission::all());

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_admin_can_view_permissions_list(): void
    {
        $response = $this->actingAs($this->admin)->get(route('permissions.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('permissions/index')
            ->has('data')
            ->has('meta')
            ->has('filters')
        );
    }
}

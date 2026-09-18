<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionsTableSeeder;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleManagementTest extends TestCase
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

    public function test_admin_can_view_roles_list(): void
    {
        $response = $this->actingAs($this->admin)->get(route('roles.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('roles/index')
            ->has('data')
            ->has('meta')
            ->has('filters')
        );
    }

    public function test_admin_can_create_role_with_permissions(): void
    {
        $perm = Permission::first();

        $response = $this->actingAs($this->admin)->post(route('roles.store'), [
            'name' => 'editor',
            'permissions' => [$perm->name],
        ]);

        $response->assertRedirect(route('roles.index'));
        $this->assertDatabaseHas('roles', ['name' => 'editor']);

        $role = Role::findByName('editor');
        $this->assertTrue($role->hasPermissionTo($perm->name));
    }

    public function test_admin_can_update_role_with_permissions(): void
    {
        $role = Role::create(['name' => 'moderator', 'guard_name' => 'web']);
        $perms = Permission::take(2)->get();

        $response = $this->actingAs($this->admin)->put(route('roles.update', $role), [
            'name' => 'senior-moderator',
            'permissions' => $perms->pluck('name')->all(),
        ]);

        $response->assertRedirect(route('roles.index'));
        $this->assertDatabaseHas('roles', ['name' => 'senior-moderator']);
        $this->assertTrue($role->fresh()->hasAllPermissions($perms->pluck('name')->all()));
    }

    public function test_admin_can_delete_role(): void
    {
        $role = Role::create(['name' => 'temporary-role', 'guard_name' => 'web']);

        $response = $this->actingAs($this->admin)->delete(route('roles.destroy', $role));

        $response->assertRedirect(route('roles.index'));
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }
}

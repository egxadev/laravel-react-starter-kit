<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionsTableSeeder;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BreadcrumbsTest extends TestCase
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

    public function test_index_routes_derive_the_resource_breadcrumb(): void
    {
        $this->actingAs($this->admin)
            ->get(route('users.index'))
            ->assertInertia(fn ($page) => $page
                ->has('breadcrumbs', 1)
                ->where('breadcrumbs.0.title', 'User')
                ->where('breadcrumbs.0.href', route('users.index'))
            );

        $this->actingAs($this->admin)
            ->get(route('permissions.index'))
            ->assertInertia(fn ($page) => $page
                ->where('breadcrumbs.0.title', 'Permission')
            );
    }

    public function test_create_and_edit_routes_derive_parent_and_action(): void
    {
        $this->actingAs($this->admin)
            ->get(route('roles.create'))
            ->assertInertia(fn ($page) => $page
                ->has('breadcrumbs', 2)
                ->where('breadcrumbs.0.title', 'Role')
                ->where('breadcrumbs.0.href', route('roles.index'))
                ->where('breadcrumbs.1.title', 'Create')
                ->where('breadcrumbs.1.href', route('roles.create'))
            );

        $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);

        $this->actingAs($this->admin)
            ->get(route('roles.edit', $role))
            ->assertInertia(fn ($page) => $page
                ->where('breadcrumbs.1.title', 'Edit')
                ->where('breadcrumbs.1.href', route('roles.edit', $role))
            );
    }

    public function test_single_segment_route_derives_a_single_breadcrumb(): void
    {
        $this->actingAs($this->admin)
            ->get(route('dashboard'))
            ->assertInertia(fn ($page) => $page
                ->has('breadcrumbs', 1)
                ->where('breadcrumbs.0.title', 'Dashboard')
                ->where('breadcrumbs.0.href', route('dashboard'))
            );
    }

    public function test_settings_routes_derive_no_breadcrumbs(): void
    {
        $this->actingAs($this->admin)
            ->get(route('profile.edit'))
            ->assertInertia(fn ($page) => $page->has('breadcrumbs', 0));
    }
}

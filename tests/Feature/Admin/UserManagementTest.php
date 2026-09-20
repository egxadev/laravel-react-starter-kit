<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionsTableSeeder;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
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

    public function test_admin_can_view_users_list(): void
    {
        $response = $this->actingAs($this->admin)->get(route('users.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('users/index')
            ->has('data')
            ->has('meta')
            ->has('filters')
        );
    }

    public function test_admin_can_create_user_with_roles(): void
    {
        $role = Role::first();

        $response = $this->actingAs($this->admin)->post(route('users.store'), [
            'name' => 'New Operator',
            'email' => 'operator@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'roles' => [$role->name],
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'operator@example.com',
            'created_by' => $this->admin->id,
        ]);

        $created = User::where('email', 'operator@example.com')->first();
        $this->assertTrue($created->hasRole($role->name));
    }

    public function test_admin_can_update_user_with_roles(): void
    {
        $targetUser = User::factory()->create();
        $role = Role::first();

        $response = $this->actingAs($this->admin)->put(route('users.update', $targetUser), [
            'name' => 'Updated Name',
            'email' => $targetUser->email,
            'roles' => [$role->name],
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Updated Name',
            'updated_by' => $this->admin->id,
        ]);

        $this->assertTrue($targetUser->fresh()->hasRole($role->name));
    }

    public function test_admin_can_delete_another_user(): void
    {
        $targetUser = User::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('users.destroy', $targetUser));

        $response->assertRedirect(route('users.index'));
        $this->assertSoftDeleted($targetUser);
        $this->assertEquals($this->admin->id, $targetUser->fresh()->deleted_by);
    }

    public function test_admin_cannot_delete_own_account_in_admin_panel(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('users.destroy', $this->admin));

        $response->assertForbidden();
        $this->assertNotSoftDeleted($this->admin);
    }

    public function test_unauthenticated_user_cannot_access_users_index(): void
    {
        $response = $this->get(route('users.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_without_create_permission_cannot_store_user(): void
    {
        $noPermUser = User::factory()->create();
        $role = Role::first();

        $response = $this->actingAs($noPermUser)->post(route('users.store'), [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'roles' => [$role->name],
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('users', ['email' => 'new@example.com']);
    }

    public function test_user_without_edit_permission_cannot_update_user(): void
    {
        $noPermUser = User::factory()->create();
        $targetUser = User::factory()->create();
        $role = Role::first();

        $response = $this->actingAs($noPermUser)->put(route('users.update', $targetUser), [
            'name' => 'Changed',
            'email' => $targetUser->email,
            'roles' => [$role->name],
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $targetUser->id, 'name' => $targetUser->name]);
    }

    public function test_user_without_delete_permission_cannot_destroy_user(): void
    {
        $noPermUser = User::factory()->create();
        $targetUser = User::factory()->create();

        $response = $this->actingAs($noPermUser)->delete(route('users.destroy', $targetUser));

        $response->assertForbidden();
        $this->assertNotSoftDeleted($targetUser);
    }

    public function test_admin_cannot_force_delete_own_account(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('users.force-delete', $this->admin));

        $response->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_admin_can_restore_and_force_delete_user(): void
    {
        $targetUser = User::factory()->create();
        $targetUser->delete();
        $this->assertSoftDeleted($targetUser);

        // Restore
        $restoreResponse = $this->actingAs($this->admin)->patch(route('users.restore', $targetUser));
        $restoreResponse->assertRedirect(route('users.index'));
        $this->assertNotSoftDeleted($targetUser);

        // Force delete
        $forceDeleteResponse = $this->actingAs($this->admin)->delete(route('users.force-delete', $targetUser));
        $forceDeleteResponse->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);
    }

    public function test_users_list_can_be_searched(): void
    {
        User::factory()->create(['name' => 'Findable Person', 'email' => 'findable@example.com']);
        User::factory()->create(['name' => 'Hidden Person', 'email' => 'hidden@example.com']);

        $response = $this->actingAs($this->admin)->get(route('users.index', ['search' => 'Findable']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('users/index')
            ->has('data', 1)
            ->where('data.0.name', 'Findable Person')
            ->where('filters.search', 'Findable')
        );
    }

    public function test_users_list_can_be_sorted_and_paginated(): void
    {
        $this->admin->update(['name' => 'Zulu Admin']);
        User::factory()->create(['name' => 'Alpha']);
        User::factory()->create(['name' => 'Bravo']);
        User::factory()->create(['name' => 'Charlie']);
        User::factory()->create(['name' => 'Delta']);
        User::factory()->create(['name' => 'Echo']);

        $response = $this->actingAs($this->admin)->get(route('users.index', [
            'sort_by' => 'name',
            'sort_dir' => 'desc',
            'per_page' => 2,
            'page' => 1,
        ]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('meta.per_page', 2)
            ->where('meta.total', 6)
            ->has('data', 2)
            ->where('data.0.name', 'Zulu Admin')
            ->where('data.1.name', 'Echo')
            ->where('filters.sort_by', 'name')
            ->where('filters.sort_dir', 'desc')
        );
    }

    public function test_users_list_can_be_filtered_to_trashed(): void
    {
        User::factory()->create(['name' => 'Active Person']);
        User::factory()->create(['name' => 'Gone Person'])->delete();

        $response = $this->actingAs($this->admin)->get(route('users.index', ['trashed' => true]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('data', 1)
            ->where('data.0.name', 'Gone Person')
            ->where('filters.trashed', true)
        );
    }
}

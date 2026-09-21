<?php

namespace Tests\Feature\Admin;

use App\Enums\Permission;
use Database\Seeders\PermissionsTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission as PermissionModel;
use Tests\TestCase;

class PermissionMatrixTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_seeder_creates_every_declared_permission(): void
    {
        $this->seed(PermissionsTableSeeder::class);

        $declared = array_column(Permission::cases(), 'value');
        sort($declared);

        $seeded = PermissionModel::pluck('name')->sort()->values()->all();

        $this->assertSame($declared, $seeded);
    }

    public function test_the_frontend_permission_constants_match_the_enum(): void
    {
        $contents = file_get_contents(resource_path('js/constants/permissions.ts'));

        preg_match_all("/:\s*['\"]([^'\"]+)['\"]/", (string) $contents, $matches);

        $frontend = array_values(array_unique($matches[1]));
        sort($frontend);

        $declared = array_column(Permission::cases(), 'value');
        sort($declared);

        $this->assertSame($declared, $frontend);
    }
}

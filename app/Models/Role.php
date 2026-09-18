<?php

namespace App\Models;

use App\Concerns\Filterable;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use Filterable;

    /**
     * Searchable and sortable columns for Filterable concern.
     *
     * @var array<string>
     */
    protected array $searchable = ['name', 'created_at'];

    /**
     * Create role and assign permissions atomically.
     *
     * @param  array<string, mixed>  $attributes
     * @param  array<string>|string  $permissions
     */
    public static function createWithPermissions(array $attributes, array|string $permissions): self
    {
        return DB::transaction(function () use ($attributes, $permissions) {
            $role = static::create([
                'name' => $attributes['name'],
            ]);

            $role->syncPermissions($permissions);

            return $role;
        });
    }

    /**
     * Update role and sync permissions atomically.
     *
     * @param  array<string, mixed>  $attributes
     * @param  array<string>|string  $permissions
     */
    public function updateWithPermissions(array $attributes, array|string $permissions): self
    {
        return DB::transaction(function () use ($attributes, $permissions) {
            $this->update([
                'name' => $attributes['name'],
            ]);

            $this->syncPermissions($permissions);

            return $this;
        });
    }
}

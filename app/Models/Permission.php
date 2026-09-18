<?php

namespace App\Models;

use App\Concerns\Filterable;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use Filterable;

    /**
     * Searchable and sortable columns for Filterable concern.
     *
     * @var array<string>
     */
    protected array $searchable = ['name', 'created_at'];
}

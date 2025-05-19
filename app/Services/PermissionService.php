<?php

namespace App\Services;

use Spatie\Permission\Models\Permission;

class PermissionService
{
    private const DEFAULT_PER_PAGE = 10;
    private const DEFAULT_SORT_BY = 'name';
    private const DEFAULT_SORT_DIR = 'asc';
    private const FILTERABLE_COLUMNS = ['id', 'name', 'created_at'];

    /**
     * Get paginated permissions with filters.
     *
     * @param array $filters
     * @return array
     */
    public function getPaginatedPermissions(array $filters): array
    {
        $perPage = (int) ($filters['per_page'] ?? self::DEFAULT_PER_PAGE);
        $page = (int) ($filters['page'] ?? 1);
        $sortBy  = in_array($sort = $filters['sort_by'] ?? self::DEFAULT_SORT_BY, self::FILTERABLE_COLUMNS) ? $sort : self::DEFAULT_SORT_BY;
        $sortDir = in_array($dir = strtolower($filters['sort_dir'] ?? self::DEFAULT_SORT_DIR), ['asc', 'desc']) ? $dir : self::DEFAULT_SORT_DIR;
        $search = trim($filters['search'] ?? '');

        $query = Permission::query();

        $query->when($search, function ($query) use ($search) {
            $query->where(function ($subQuery) use ($search) {
                foreach (self::FILTERABLE_COLUMNS as $column) {
                    $subQuery->orWhere($column, 'like', "%{$search}%");
                }
            });
        });

        $query->orderBy($sortBy, $sortDir);

        $permissions = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'permissions' => $permissions->items(),
            'meta' => [
                'current_page' => $permissions->currentPage(),
                'last_page' => $permissions->lastPage(),
                'per_page' => $permissions->perPage(),
                'total' => $permissions->total(),
                'from' => $permissions->firstItem(),
                'to' => $permissions->lastItem(),
            ],
            'filters' => [
                'search' => $search,
                'sort_by' => $sortBy,
                'sort_dir' => $sortDir,
            ]
        ];
    }

    /**
     * Create a new permission.
     *
     * @param array $data
     * @return Permission
     */
    public function createPermission(array $data): Permission
    {
        return Permission::create([
            'name' => $data['name'],
        ]);
    }

    /**
     * Update existing permission.
     *
     * @param Permission $permission
     * @param array $data
     * @return Permission
     */
    public function updatePermission(Permission $permission, array $data): Permission
    {
        $permission->update([
            'name' => $data['name'],
        ]);

        return $permission;
    }

    /**
     * Delete permission by ID.
     *
     * @param string $id
     * @return bool
     */
    public function deletePermission(string $id): bool
    {
        return Permission::findOrFail($id)->delete();
    }
}

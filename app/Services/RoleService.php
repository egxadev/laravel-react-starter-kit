<?php

namespace App\Services;

use Spatie\Permission\Models\Role;

class RoleService
{
    private const DEFAULT_PER_PAGE = 10;
    private const DEFAULT_SORT_BY = 'name';
    private const DEFAULT_SORT_DIR = 'asc';
    private const FILTERABLE_COLUMNS = ['id', 'name', 'created_at'];

    /**
     * Get paginated roles with filters.
     *
     * @param array $filters
     * @return array
     */
    public function getPaginatedRoles(array $filters): array
    {
        $perPage = (int) ($filters['per_page'] ?? self::DEFAULT_PER_PAGE);
        $page = (int) ($filters['page'] ?? 1);
        $sortBy  = in_array($sort = $filters['sort_by'] ?? self::DEFAULT_SORT_BY, self::FILTERABLE_COLUMNS) ? $sort : self::DEFAULT_SORT_BY;
        $sortDir = in_array($dir = strtolower($filters['sort_dir'] ?? self::DEFAULT_SORT_DIR), ['asc', 'desc']) ? $dir : self::DEFAULT_SORT_DIR;

        $search = trim($filters['search'] ?? '');

        $query = Role::query();

        $query->when($search, function ($query) use ($search) {
            $query->where(function ($subQuery) use ($search) {
                foreach (self::FILTERABLE_COLUMNS as $column) {
                    $subQuery->orWhere($column, 'like', "%{$search}%");
                }
            });
        });

        $query->orderBy($sortBy, $sortDir);

        $roles = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'roles' => $roles->items(),
            'meta' => [
                'current_page' => $roles->currentPage(),
                'last_page' => $roles->lastPage(),
                'per_page' => $roles->perPage(),
                'total' => $roles->total(),
                'from' => $roles->firstItem(),
                'to' => $roles->lastItem(),
            ],
            'filters' => compact('search', 'sortBy', 'sortDir'),
        ];
    }

    /**
     * Get role with permissions by ID.
     *
     * @param string $id
     * @return Role
     */
    public function getRoleById(string $id): Role
    {
        return Role::with('permissions')->findOrFail($id);
    }

    /**
     * Create a new role and assign permissions.
     *
     * @param array $data
     * @return Role
     */
    public function createRole(array $data): Role
    {
        $role = Role::create(['name' => $data['name']]);
        $role->givePermissionTo($data['permissions']);

        return $role;
    }

    /**
     * Update role and sync permissions.
     *
     * @param Role $role
     * @param array $data
     * @return Role
     */
    public function updateRole(Role $role, array $data): Role
    {
        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permissions']);

        return $role;
    }

    /**
     * Delete role by ID.
     *
     * @param string $id
     * @return bool
     */
    public function deleteRole(string $id): bool
    {
        return Role::findOrFail($id)->delete();
    }
}

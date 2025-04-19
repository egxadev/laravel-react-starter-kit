<?php

namespace App\Services;

use Spatie\Permission\Models\Permission;

class PermissionService
{
    public function getPaginatedPermissions(array $filters): array
    {
        $perPage = $filters['per_page'] ?? 10;
        $page = $filters['page'] ?? 1;
        $sortBy = $filters['sort_by'] ?? 'name';
        $sortDir = $filters['sort_dir'] ?? 'asc';
        $search = $filters['search'] ?? '';

        $query = Permission::query();

        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $validSortColumns = ['id', 'name', 'created_at'];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'name';
        $sortDir = $sortDir === 'desc' ? 'desc' : 'asc';

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
            ],
        ];
    }

    public function createPermission(array $data): Permission
    {
        $permission = Permission::create([
            'name' => $data['name'],
        ]);

        return $permission;
    }

    public function updatePermission(Permission $permission, array $data): Permission
    {
        $permission->update([
            'name' => $data['name'],
        ]);

        return $permission;
    }

    public function deletePermission(string $id): bool
    {
        $permission = Permission::findOrFail($id);
        return $permission->delete();
    }
}

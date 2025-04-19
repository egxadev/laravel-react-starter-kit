<?php

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function getPaginatedRoles(array $filters): array
    {
        $perPage = $filters['per_page'] ?? 10;
        $page = $filters['page'] ?? 1;
        $sortBy = $filters['sort_by'] ?? 'name';
        $sortDir = $filters['sort_dir'] ?? 'asc';
        $search = $filters['search'] ?? '';

        $query = Role::query();

        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $validSortColumns = ['id', 'name', 'created_at'];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'id';
        $sortDir = $sortDir === 'desc' ? 'desc' : 'asc';

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
            'filters' => [
                'search' => $search,
                'sort_by' => $sortBy,
                'sort_dir' => $sortDir,
            ],
        ];
    }

    public function getRoleById(string $id): Role
    {
        return Role::with('permissions')->findOrFail($id);
    }

    public function createRole(array $data): Role
    {
        $role = Role::create(['name' => $data['name']]);

        $role->givePermissionTo($data['permissions']);

        return $role;
    }

    public function updateRole(Role $role, array $data): Role
    {
        $role->update(['name' => $data['name']]);

        $role->syncPermissions($data['permissions']);

        return $role;
    }

    public function deleteRole(string $id): bool
    {
        $role = Role::findOrFail($id);

        return $role->delete();
    }
}

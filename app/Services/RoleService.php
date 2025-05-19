<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
            'data'  => $roles->items(),
            'meta'  => [
                'current_page'  => $roles->currentPage(),
                'last_page'     => $roles->lastPage(),
                'per_page'      => $roles->perPage(),
                'total'         => $roles->total(),
                'from'          => $roles->firstItem(),
                'to'            => $roles->lastItem(),
            ],
            'filters' => [
                'search' => $search,
                'sort_by' => $sortBy,
                'sort_dir' => $sortDir,
            ]
        ];
    }

    /**
     * Create a new role and assign permissions.
     *
     * @param array $data
     * @return array
     */
    public function createRole(array $data): array
    {
        try {
            $role = \DB::transaction(function () use ($data) {
                $role = Role::create([
                    'name'          => $data['name'],
                    'created_by'    => auth()->id()
                ]);
                $role->givePermissionTo($data['permissions']);

                return $role;
            });

            return [
                'success'   => true,
                'message'   => 'Role created successfully.',
                'role'      => $role,
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to create role: ' . $e->getMessage());
            return [
                'success'   => false,
                'message'   => 'Failed to create role.',
            ];
        }
    }

    /**
     * Update role and sync permissions.
     *
     * @param Role $role
     * @param array $data
     * @return Role
     */
    public function updateRole(Role $role, array $data): array
    {
        try {
            \DB::transaction(function () use ($role, $data) {
                $role->update([
                    'name'          => $data['name'],
                    'updated_by'    => auth()->id()
                ]);
                $role->syncPermissions($data['permissions']);
            });

            return [
                'success'   => true,
                'message'   => 'Role updated successfully.',
                'data'      => $role->fresh(),
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'success'   => false,
                'message'   => 'Role not found.',
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to update role: ' . $e->getMessage());
            return [
                'success'   => false,
                'message'   => 'Failed to update role.',
            ];
        }
    }

    /**
     * Delete role by ID.
     *
     * @param string $id
     * @return array
     */
    public function deleteRole(string $id): array
    {
        try {
            $role = Role::findOrFail($id);

            return \DB::transaction(function () use ($role) {
                $role->update(['deleted_by' => auth()->id()]);
                $role->delete();

                return [
                    'success'   => true,
                    'message'   => 'Role deleted successfully.'
                ];
            });
        } catch (ModelNotFoundException $e) {
            return [
                'success'   => false,
                'message'   => 'Role not found.'
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to delete role: ' . $e->getMessage());
            return [
                'success'   => false,
                'message'   => 'Failed to delete role.'
            ];
        }
    }
}

<?php

namespace App\Services;

use App\Traits\ResponseFormatter;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoleService
{
    use ResponseFormatter;

    private const DEFAULT_PER_PAGE = 10;
    private const DEFAULT_SORT_BY = 'name';
    private const DEFAULT_SORT_DIR = 'asc';
    private const FILTERABLE_COLUMNS = ['name', 'created_at'];

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

        $data = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->paginatedResponse($data->items(), [
            'current_page'  => $data->currentPage(),
            'last_page'     => $data->lastPage(),
            'per_page'      => $data->perPage(),
            'total'         => $data->total(),
            'from'          => $data->firstItem(),
            'to'            => $data->lastItem(),
        ], [
            'search'        => $search,
            'sort_by'       => $sortBy,
            'sort_dir'      => $sortDir,
        ]);
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
            $createdData = \DB::transaction(function () use ($data) {
                $role = Role::create([
                    'name'          => $data['name'],
                    'created_by'    => auth()->id()
                ]);
                $role->givePermissionTo($data['permissions']);

                return $role;
            });

            return $this->successResponse($createdData, 'Role created successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to create role: ' . $e->getMessage());
            return $this->errorResponse('Failed to create role.');
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
            $updatedData = \DB::transaction(function () use ($role, $data) {
                $role->update([
                    'name'          => $data['name'],
                    'updated_by'    => auth()->id()
                ]);
                $role->syncPermissions($data['permissions']);
                return $role;
            });

            return $this->successResponse($updatedData, 'Role updated successfully.');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Role not found.');
        } catch (\Exception $e) {
            \Log::error('Failed to update role: ' . $e->getMessage());
            return $this->errorResponse('Failed to update role.');
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

            \DB::transaction(function () use ($role) {
                $role->update(['deleted_by' => auth()->id()]);
                $role->delete();
            });

            return $this->successResponse(null, 'Role deleted successfully.');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Role not found.');
        } catch (\Exception $e) {
            \Log::error('Failed to delete role: ' . $e->getMessage());
            return $this->errorResponse('Failed to delete role.');
        }
    }
}

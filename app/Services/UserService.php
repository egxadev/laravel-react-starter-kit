<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserService
{
    private const DEFAULT_PER_PAGE = 10;
    private const DEFAULT_SORT_BY = 'name';
    private const DEFAULT_SORT_DIR = 'asc';
    private const FILTERABLE_COLUMNS = ['id', 'name', 'email', 'created_at'];

    /**
     * Get paginated users with filters.
     *
     * @param array $filters
     * @return array
     */
    public function getPaginatedUsers(array $filters): array
    {
        $perPage = (int) ($filters['per_page'] ?? self::DEFAULT_PER_PAGE);
        $page = (int) ($filters['page'] ?? 1);
        $sortBy  = in_array($sort = $filters['sort_by'] ?? self::DEFAULT_SORT_BY, self::FILTERABLE_COLUMNS) ? $sort : self::DEFAULT_SORT_BY;
        $sortDir = in_array($dir = strtolower($filters['sort_dir'] ?? self::DEFAULT_SORT_DIR), ['asc', 'desc']) ? $dir : self::DEFAULT_SORT_DIR;

        $search = trim($filters['search'] ?? '');

        $query = User::query();

        $query->when($search, function ($query) use ($search) {
            $query->where(function ($subQuery) use ($search) {
                foreach (self::FILTERABLE_COLUMNS as $column) {
                    $subQuery->orWhere($column, 'like', "%{$search}%");
                }
            });
        });

        $query->orderBy($sortBy, $sortDir);

        $users = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data'  => $users->items(),
            'meta'  => [
                'current_page'  => $users->currentPage(),
                'last_page'     => $users->lastPage(),
                'per_page'      => $users->perPage(),
                'total'         => $users->total(),
                'from'          => $users->firstItem(),
                'to'            => $users->lastItem(),
            ],
            'filters' => [
                'search' => $search,
                'sort_by' => $sortBy,
                'sort_dir' => $sortDir,
            ]
        ];
    }

    /**
     * Create new user and assign roles.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): array
    {
        try {
            $user = \DB::transaction(function () use ($data) {
                $user = User::create([
                    'name'          => $data['name'],
                    'email'         => $data['email'],
                    'password'      => bcrypt($data['password']),
                    'created_by'    => auth()->id(),
                ]);

                $user->assignRole($data['roles']);

                return $user;
            });

            return [
                'success'   => true,
                'message'   => 'User created successfully.',
                'user'      => $user,
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to create user: ' . $e->getMessage());
            return [
                'success'   => false,
                'message'   => 'Failed to create user.',
            ];
        }
    }

    /**
     * Update user data and sync roles.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateUser(User $user, array $data): array
    {
        try {
            $updateData = [
                'name'          => $data['name'],
                'email'         => $data['email'],
                'updated_by'    => auth()->id(),
            ];

            if (!empty($data['password'])) {
                $updateData['password'] = bcrypt($data['password']);
            }

            \DB::transaction(function () use ($user, $updateData, $data) {
                $user->update($updateData);
                $user->syncRoles($data['roles']);
            });

            return [
                'success'   => true,
                'message'   => 'User updated successfully.',
                'data'      => $user->fresh(),
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'success'   => false,
                'message'   => 'User not found.',
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to update user: ' . $e->getMessage());
            return [
                'success'   => false,
                'message'   => 'Failed to update user.',
            ];
        }
    }

    /**
     * Delete user by ID.
     *
     * @param string $id
     * @return array
     */
    public function deleteUser(string $id): array
    {
        try {
            $user = User::findOrFail($id);

            return \DB::transaction(function () use ($user) {
                $user->update(['deleted_by' => auth()->id()]);
                $user->delete();

                return [
                    'success'   => true,
                    'message'   => 'User deleted successfully.'
                ];
            });
        } catch (ModelNotFoundException $e) {
            return [
                'success'   => false,
                'message'   => 'User not found.'
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to delete user: ' . $e->getMessage());
            return [
                'success'   => false,
                'message'   => 'Failed to delete user.'
            ];
        }
    }
}

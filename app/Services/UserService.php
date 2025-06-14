<?php

namespace App\Services;

use App\Models\User;
use App\Traits\ResponseFormatter;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserService
{
    use ResponseFormatter;

    private const DEFAULT_PER_PAGE = 10;
    private const DEFAULT_SORT_BY = 'name';
    private const DEFAULT_SORT_DIR = 'asc';
    private const FILTERABLE_COLUMNS = ['name', 'email', 'created_at'];

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
     * Create new user and assign roles.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): array
    {
        try {
            $createdData = \DB::transaction(function () use ($data) {
                $user = User::create([
                    'name'          => $data['name'],
                    'email'         => $data['email'],
                    'password'      => bcrypt($data['password']),
                    'created_by'    => auth()->id(),
                ]);

                $user->assignRole($data['roles']);

                return $user;
            });

            return $this->successResponse($createdData, 'User created successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to create user: ' . $e->getMessage());
            return $this->errorResponse('Failed to create user.');
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
            if (!empty($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            }

            $updatedData = \DB::transaction(function () use ($user, $data) {
                $user->update([
                    'name'          => $data['name'],
                    'email'         => $data['email'],
                    'password'      => $data['password'] ?? $user->password,
                    'updated_by'    => auth()->id(),
                ]);
                $user->syncRoles($data['roles']);

                return $user;
            });
            
            return $this->successResponse($updatedData, 'User updated successfully.');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('User not found.');
        } catch (\Exception $e) {
            \Log::error('Failed to update user: ' . $e->getMessage());
            return $this->errorResponse('Failed to update user.');
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
            $currentUser = auth()->user();
            $isSelfDelete = $user->id === $currentUser->id;

            \DB::transaction(function () use ($user, $currentUser) {
                $user->update(['deleted_by' => $currentUser->id]);
                $user->delete();
            });

            if ($isSelfDelete) {
                auth()->logout();
                session()->invalidate();
                session()->regenerateToken();

                return $this->successResponse(null, 'Your account has been deleted successfully.', [
                    'redirect' => route('login')
                ]);
            }

            return $this->successResponse(null, 'User deleted successfully.');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('User not found.');
        } catch (\Exception $e) {
            \Log::error('Failed to delete user: ' . $e->getMessage());
            return $this->errorResponse('Failed to delete user.');
        }
    }
}

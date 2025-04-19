<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getPaginatedUsers(array $filters): array
    {
        $perPage = $filters['per_page'] ?? 10;
        $page = $filters['page'] ?? 1;
        $sortBy = $filters['sort_by'] ?? 'name';
        $sortDir = $filters['sort_dir'] ?? 'asc';
        $search = $filters['search'] ?? '';

        $query = User::query();

        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
        }

        $validSortColumns = ['id', 'name', 'email', 'created_at'];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'id';
        $sortDir = $sortDir === 'desc' ? 'desc' : 'asc';

        $query->orderBy($sortBy, $sortDir);

        $users = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'users' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
            ],
            'filters' => [
                'search' => $search,
                'sort_by' => $sortBy,
                'sort_dir' => $sortDir,
            ],
        ];
    }

    public function createUser(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);

        $user->assignRole($data['roles']);

        return $user;
    }

    public function updateUser(User $user, array $data): User
    {
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email']
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = bcrypt($data['password']);
        }

        $user->update($updateData);
        $user->syncRoles($data['roles']);

        return $user;
    }

    public function deleteUser(string $id): bool
    {
        $user = User::findOrFail($id);
        return $user->delete();
    }
}

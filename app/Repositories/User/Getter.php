<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class Getter
{
    /**
     * Get one user by id
     */
    public function getById(int $id): ?array
    {
        return User::findOrFail($id)->toArray();
    }

    /**
     * Get users with pagination.
     */
    public function getPagination(array $input): array
    {
        $query = DB::table('users')->select([
            'users.id',
            'users.name',
            'users.email',
            'users.email_verified_at',
            'users.is_admin',
            'users.created_at',
            'users.updated_at',
        ])->orderBy('users.name', 'asc');

        return $query->paginate($input['per_page'], ['*'], 'page', $input['page'])->toArray();
    }

    /**
     * Check if user is admin.
     */
    public function isUserAsAdmin(int $userId): bool
    {
        return DB::table('users')->whereRaw('(id = ? AND is_admin = 1)', [$userId])->exists();
    }
}

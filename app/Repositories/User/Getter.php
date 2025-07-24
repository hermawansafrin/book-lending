<?php

namespace App\Repositories\User;

use App\Models\User;

class Getter
{
    /**
     * Get one user by id
     */
    public function getById(int $id): ?array
    {
        return User::findOrFail($id)->toArray();
    }
}

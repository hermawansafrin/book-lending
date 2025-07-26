<?php

namespace App\Repositories\User;

use App\Models\User;

class Updater
{
    /**
     * Make user as admin.
     */
    public function makeUserAsAdmin(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->is_admin = 1;
        $user->save();
    }
}

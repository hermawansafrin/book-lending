<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Creator
{
    /**
     * Create a new user.
     */
    public function create(array $input): int
    {
        $user = new User; // defined creation data like object for easy tracking input data
        $user->name = $input['name'];
        $user->email = $input['email'];
        $user->password = Hash::make($input['password']);
        $user->save();

        return $user->id;
    }
}

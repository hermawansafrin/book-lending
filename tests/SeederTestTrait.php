<?php

namespace Tests;

use Database\Seeders\BookSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

trait SeederTestTrait
{
    /**
     * Create user with only 2 user with details :
     *  - only 2 user exists in this db
     *  - one is admin
     *  - one is member
     */
    public function makeSureOneAdminAndOneMember(): void
    {
        DB::table('users')->truncate();

        $this->createUser([
            'name' => 'admin',
            'email' => 'admin@mail.test',
            'password' => Hash::make('somePassword99!'),
            'is_admin' => 1,
        ]);

        $this->createUser([
            'name' => 'member 1',
            'email' => 'member@mail.test',
            'password' => Hash::make('somePassword99!'),
            'is_admin' => 0,
        ]);
    }

    /**
     * Create a user
     */
    public function createUser(array $data = []): void
    {
        $defaultInput = [
            'name' => 'user 1',
            'email' => 'user1@mail.test',
            'password' => Hash::make('somePassword99!'),
            'is_admin' => 0,
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ];

        if (! empty($data)) {
            $defaultInput = array_merge($defaultInput, $data);
        }

        DB::table('users')->insert($defaultInput);
    }

    /**
     * Seed book for check
     */
    public function seedBook(): void
    {
        if (! DB::table('books')->exists()) {
            Artisan::call('db:seed', ['--class' => BookSeeder::class]);
        }
    }
}

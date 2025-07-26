<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try {
            // create admin user (assumption it is the user can do all of function library)
            DB::table('users')->insert([
                'name' => 'Admin',
                'email' => 'admin@mail.test',
                'password' => Hash::make('123456'),
                'is_admin' => 1,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ]);

            // create users from registration (assumption it is the member of library)
            $maxUser = 3;
            for ($i = 1; $i <= 3; $i++) { // every seed run, expected run 3 loop user but with max 3 users on tables
                if (DB::table('users')->count() < $maxUser) {
                    DB::table('users')->insert([
                        'name' => 'User '.$i,
                        'email' => 'user'.$i.'@mail.test',
                        'password' => Hash::make('123456'),
                        'is_admin' => 0,
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

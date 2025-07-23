<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try {
            $maxBook = 5;
            for ($i = 1; $i <= $maxBook; $i++) {
                if (DB::table('books')->count() < $maxBook) {
                    DB::table('books')->insert([
                        'title' => 'Book '.$i,
                        'author' => 'Author '.$i,
                        'available_copies' => rand(10, 100), // random copies
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

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookSeeder extends Seeder
{
    public const BOOKS_COUNT = 5; // only default for seeder

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try {
            $books = [
                [
                    'title' => "Harry Potter and the sorcerer's stone",
                    'author' => 'J.K. Rowling',
                    'available_copies' => rand(10, 100),
                    'created_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                ],
                [
                    'title' => 'The Hobbit',
                    'author' => 'J.R.R. Tolkien',
                    'available_copies' => rand(10, 100),
                    'created_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                ],
                [
                    'title' => 'Laskar Pelangi',
                    'author' => 'Andrea Hirata',
                    'available_copies' => rand(10, 100),
                    'created_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                ],
                [
                    'title' => 'The Da Vinci Code',
                    'author' => 'Dan Brown',
                    'available_copies' => rand(10, 100),
                    'created_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                ],
                [
                    'title' => 'The Girl with the Dragon Tattoo',
                    'author' => 'Stieg Larsson',
                    'available_copies' => rand(10, 100),
                    'created_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                ],
            ];

            DB::table('books')->insert($books);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get random book data
     */
    private function getRandomBookData(): array
    {
        $books = [];
        $maxBook = 5;

        for ($i = 1; $i <= $maxBook; $i++) {
            $books[] = [
                'title' => 'Book '.$i,
                'author' => 'Author '.$i,
                'available_copies' => rand(10, 100), // random copies
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ];
        }

        return $books;
    }
}

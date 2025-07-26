<?php

namespace Tests\Feature\Book;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BookTestCase extends TestCase
{
    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Get available copies of book
     */
    protected function getAvailableCopies(int $bookId): int
    {
        return DB::table('books')->select('available_copies')->where('id', $bookId)->first()->available_copies;
    }

    /**
     * Make loan book
     */
    protected function makeLoanBook(int $bookId, int $userId): void
    {
        DB::table('loans')->insert([
            'book_id' => $bookId,
            'user_id' => $userId,
            'loan_date' => now()->toDateTimeString(),
            'return_date' => null,
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ]);
    }
}

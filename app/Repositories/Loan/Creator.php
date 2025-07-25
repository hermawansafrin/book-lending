<?php

namespace App\Repositories\Loan;

use App\Models\Loan;
use App\Repositories\Book\BookRepository;

class Creator
{
    /**
     * Lend a book to a user
     */
    public function create(array $input): int
    {
        $loan = new Loan; // create new loan instance with object format for easy tracking
        $loan->book_id = $input['book_id'];
        $loan->user_id = $input['user_id'];
        $loan->loan_date = now()->toDateString();
        $loan->save();

        // after loan success, available on book must be decreased
        app(BookRepository::class)->increaseOrDecreaseAvailableCopies($input['book_id'], false);

        return $loan->id;
    }
}

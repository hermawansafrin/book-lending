<?php

namespace App\Repositories\Loan;

use App\Models\Loan;
use App\Repositories\Book\BookRepository;
use Illuminate\Support\Facades\Cache;

class Creator
{
    /**
     * Lend a book to a user
     */
    public function create(array $input): int
    {
        // use atomic lock to prevent race condition when duplicating loan book user
        $lock = Cache::lock('loan-'.$input['book_id'].'-'.$input['user_id'].'-create', config('values.atomic_lock.default_timeout'));

        try {
            $lock->block(config('values.atomic_lock.default_block_timeout'));
            $loan = new Loan; // create new loan instance with object format for easy tracking
            $loan->book_id = $input['book_id'];
            $loan->user_id = $input['user_id'];
            $loan->loan_date = now()->toDateString();
            $loan->save();
        } finally {
            $lock->release();
        }

        // after loan success, available on book must be decreased
        app(BookRepository::class)->increaseOrDecreaseAvailableCopies($input['book_id'], false);

        return $loan->id;
    }
}

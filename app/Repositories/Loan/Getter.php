<?php

namespace App\Repositories\Loan;

use App\Models\Loan;
use Illuminate\Support\Facades\DB;

class Getter
{
    /**
     * Check if the book is already being lent to the user and has not been returned
     */
    public function checkBookLendingOnGoing(int $bookId, int $userId): bool
    {
        // use query builder and whereRaw for improvement performance for simple data
        return DB::table('loans')
            ->whereRaw('(loans.book_id = ? AND loans.user_id = ? AND loans.return_date IS NULL)', [
                $bookId,
                $userId,
            ])
            ->exists();
    }

    /**
     * Get the last on going loan id by book id and user id
     */
    public function getLastOnGoingLoanId(int $bookId, int $userId): int
    {
        // use query builder and whereRaw for improvement performace for simple data
        return DB::table('loans')
            ->whereRaw('(loans.book_id = ? AND loans.user_id = ? AND loans.return_date IS NULL)', [
                $bookId,
                $userId,
            ])->orderBy('id', 'desc')->first()->id;
    }

    /**
     * Find a loan by id
     */
    public function findOne(int $id): array
    {
        return Loan::select([
            'id',
            'book_id',
            'user_id',
            'loan_date',
            'return_date',
            'created_at',
            'updated_at',
        ])->with([
            'user:id,name,email',
            'book:id,title,author',
        ])->findOrFail($id)->toArray();
    }
}

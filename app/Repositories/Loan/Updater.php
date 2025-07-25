<?php

namespace App\Repositories\Loan;

use App\Repositories\Book\BookRepository;
use Illuminate\Support\Facades\DB;

class Updater
{
    /**
     * Return a book by loan id
     */
    public function return(int $id, array $input): void
    {
        // using query builder for simple execution
        DB::table('loans')->whereRaw('(id = ?)', [$id])->update([
            'return_date' => now()->toDateString(),
            'updated_at' => now()->toDateTimeString(),
        ]);

        // after return success, available on book must be increased
        app(BookRepository::class)->increaseOrDecreaseAvailableCopies($input['book_id'], true);
    }
}

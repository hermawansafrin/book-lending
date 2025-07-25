<?php

namespace App\Repositories\Book;

use Illuminate\Support\Facades\DB;

class Updater
{
    /**
     * Increase or decrease the available copies of a book by id
     * its for after lend / return a book process has been finished
     */
    public function increaseOrDecreaseAvailableCopies(int $id, bool $isIncrease): void
    {
        // using query builder for simple execution
        DB::table('books')->whereRaw('(id = ?)', [$id])->update([
            'available_copies' => DB::raw('available_copies + '.($isIncrease ? 1 : -1)),
            'updated_at' => now()->toDateTimeString(),
        ]);
    }
}

<?php

namespace App\Repositories\Book;

use App\Models\Book;

class Creator
{
    /**
     * Create a new book
     */
    public function create(array $input): int
    {
        $book = new Book; // defined creation data like object for easy tracking input data
        $book->title = $input['title'];
        $book->author = $input['author'];
        $book->available_copies = $input['available_copies'];
        $book->save();

        return $book->id;
    }
}

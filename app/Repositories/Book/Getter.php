<?php

namespace App\Repositories\Book;

use App\Models\Book;
use Illuminate\Support\Facades\DB;

class Getter
{
    /**
     * Get books with pagination
     */
    public function pagination(array $input): array
    {
        // TODO: do with cache
        $query = Book::select([
            'books.id',
            'books.title',
            'books.author',
            'books.available_copies',
            'books.created_at',
            'books.updated_at',
        ]);

        // search by title or author
        if (! empty($input['search'])) {
            $query->whereRaw('(books.title LIKE ? OR books.author LIKE ?)', [
                "%{$input['search']}%",
                "%{$input['search']}%",
            ]);
        }

        // make sure orderred data
        $query->orderBy($input['sort_by'], $input['order_by']);

        return $query->paginate($input['per_page'], ['*'], 'page', $input['page'])->toArray();
    }

    /**
     * Find a book by id
     */
    public function findOne(int $id): array
    {
        return Book::findOrFail($id)->toArray();
    }

    /**
     * Get if title and author exists
     */
    public function getIsTitleAndAuthorExists(string $title, string $author): bool
    {
        // cause its only for check mostly, its better i used query builder
        // because on excercise only used this column, so i check by lower and trim the data
        return DB::table('books')
            ->whereRaw('LOWER(TRIM(title)) = ?', [trim(strtolower($title))])
            ->whereRaw('LOWER(TRIM(author)) = ?', [trim(strtolower($author))])
            ->exists();
    }
}

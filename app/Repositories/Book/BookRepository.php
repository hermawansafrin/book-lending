<?php

namespace App\Repositories\Book;

class BookRepository
{
    /**
     * Get books with pagination
     */
    public function pagination(array $input)
    {
        return app(Getter::class)->pagination($input);
    }

    /**
     * Create a new book and return the created data
     */
    public function create(array $input): array
    {
        $createdBookId = app(Creator::class)->create($input);

        return $this->findOne($createdBookId);
    }

    /**
     * Find a book by id
     */
    private function findOne(int $id): array
    {
        return app(Getter::class)->findOne($id);
    }

    /**
     * Check if the book has available copies
     */
    public function bookHasAvailableCopies(int $id): bool
    {
        return app(Getter::class)->bookHasAvailableCopies($id);
    }

    /**
     * Get if title and author exists
     */
    public function getIsTitleAndAuthorExists(string $title, string $author): bool
    {
        return app(Getter::class)->getIsTitleAndAuthorExists($title, $author);
    }

    /**
     * Increase or decrease the available copies of a book by id
     * its for after lend / return a book process has been finished
     */
    public function increaseOrDecreaseAvailableCopies(int $id, bool $isIncrease): void
    {
        app(Updater::class)->increaseOrDecreaseAvailableCopies($id, $isIncrease);
    }
}

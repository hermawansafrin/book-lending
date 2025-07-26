<?php

namespace App\Repositories\Loan;

class LoanRepository
{
    /**
     * Check if the book is already being lent to the user and has not been returned
     */
    public function checkBookLendingOnGoing(int $bookId, int $userId): bool
    {
        return app(Getter::class)->checkBookLendingOnGoing($bookId, $userId);
    }

    /**
     * Get the last on going loan id by book id and user id
     */
    public function getLastOnGoingLoanId(int $bookId, int $userId): int
    {
        return app(Getter::class)->getLastOnGoingLoanId($bookId, $userId);
    }

    /**
     * Lend a book to a user
     */
    public function lend(array $input): array
    {
        $loanId = app(Creator::class)->create($input);

        return $this->findOne($loanId);
    }

    /**
     * Return a book by loan id
     */
    public function return(int $id, array $input): array
    {
        app(Updater::class)->return($id, $input);

        return $this->findOne($id);
    }

    /**
     * Find a loan by id
     */
    private function findOne(int $id): array
    {
        return app(Getter::class)->findOne($id);
    }
}

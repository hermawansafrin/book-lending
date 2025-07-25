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
     * Lend a book to a user
     */
    public function lend(array $input): array
    {
        $loanId = app(Creator::class)->create($input);

        return $this->findOne($loanId);
    }

    /**
     * Find a loan by id
     */
    private function findOne(int $id): array
    {
        return app(Getter::class)->findOne($id);
    }
}

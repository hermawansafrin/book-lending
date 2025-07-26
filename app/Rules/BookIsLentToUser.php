<?php

namespace App\Rules;

use App\Repositories\Loan\LoanRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Rule to check if the book is lent to the user
 */
class BookIsLentToUser implements ValidationRule
{
    private int $userId;

    /**
     * Create a new rule instance.
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // must have on going loan
        $mustHaveOnGoingLoan = app(LoanRepository::class)->checkBookLendingOnGoing($value, $this->userId);

        if (! $mustHaveOnGoingLoan) {
            $fail(__('validation.book.not_borrowed'));
        }
    }
}

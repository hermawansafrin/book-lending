<?php

namespace App\Rules;

use App\Repositories\Loan\LoanRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Prevent duplicate book lending for each user
 */
class PreventDuplicateBookLending implements ValidationRule
{
    private int $userId;

    /**
     * Constructor
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
        if (app(LoanRepository::class)->checkBookLendingOnGoing($value, $this->userId)) {
            $fail(__('validation.book.already_borrowed'));
        }
    }
}

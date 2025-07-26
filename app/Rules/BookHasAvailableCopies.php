<?php

namespace App\Rules;

use App\Repositories\Book\BookRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BookHasAvailableCopies implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! app(BookRepository::class)->bookHasAvailableCopies($value)) {
            $fail(__('validation.book.no_available_copies'));
        }
    }
}

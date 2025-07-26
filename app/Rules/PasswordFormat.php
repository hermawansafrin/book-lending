<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PasswordFormat implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // must as a string
        if (! is_string($value)) {
            $fail(__('validation.input_must_be_text', ['attribute' => $attribute]));
        }

        // must have at least 1 uppercase letter
        if (! preg_match('/[A-Z]/', $value)) {
            $fail(__('validation.string_must_at_least_one_uppercase', ['attribute' => $attribute]));
        }

        // must have at least 1 number
        if (! preg_match('/[0-9]/', $value)) {
            $fail(__('validation.string_must_have_one_number', ['attribute' => $attribute]));
        }

        // must have at least 1 special character
        if (! preg_match('/[!@#$%^&*()\-_=~+{};:,<.>]/', $value)) {
            $fail(__('validation.string_must_at_least_one_special_char', ['attribute' => $attribute]));
        }
    }
}

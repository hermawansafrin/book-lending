<?php

namespace App\Rules;

use App\Repositories\User\UserRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UserAlreadyAsAdmin implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (app(UserRepository::class)->isUserAsAdmin($value)) {
            $fail(__('validation.user_already_as_admin'));
        }
    }
}

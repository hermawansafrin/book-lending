<?php

namespace App\Http\Requests\Auth;

trait RuleTrait
{
    /**
     * Get the rules for the email field for login auth.
     */
    public function getEmailRules(): array
    {
        return [
            'bail',
            'required',
            'min:5',
            'max:100',
            'email',
            'exists:users,email',
        ];
    }

    /**
     * Get the rules for the password field for login auth.
     */
    public function getPasswordRules(): array
    {
        return [
            'bail',
            'required',
            'min:6',
            'max: 16',
        ];
    }
}

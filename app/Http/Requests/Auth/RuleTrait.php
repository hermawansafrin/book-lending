<?php

namespace App\Http\Requests\Auth;

use App\Rules\PasswordFormat;

trait RuleTrait
{
    /**
     * Get the rules for the email field for login auth.
     */
    public function getEmailRules(bool $isLogin): array
    {
        $rules = [
            'bail',
            'required',
            'min:5',
            'max:100',
            'email',
        ];

        if ($isLogin) {
            $rules[] = 'exists:users,email'; // must be exists email
        } else {
            $rules[] = 'unique:users,email'; // must be unique email
        }

        return $rules;
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

    /**
     * Get the rules for the name field for register auth.
     */
    public function getNameRules(): array
    {
        return [
            'bail',
            'required',
            'min:3',
            'max:200',
        ];
    }

    /**
     * Get the rules for the password field for register user.
     */
    public function getPasswordRegisterRules(bool $mustConfirm): array
    {
        $rules = $this->getPasswordRules();

        // must as confirmed password
        if ($mustConfirm) {
            $rules[] = 'confirmed';
            $rules[] = new PasswordFormat;
        }

        return $rules;
    }
}

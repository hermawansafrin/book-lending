<?php

namespace App\Http\Requests\User;

trait RuleTrait
{
    /**
     * Get the validation rules for the user id.
     */
    protected function getUserIdRules(): array
    {
        return [
            'bail',
            'required',
            'integer',
            'numeric',
            'exists:users,id',
        ];
    }
}

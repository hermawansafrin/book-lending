<?php

namespace App\Http\Requests\Book;

trait RuleTrait
{
    /**
     * Get the validation rules that apply to the book_id field.
     */
    public function getBookIdRules(): array
    {
        return [
            'bail',
            'required',
            'integer',
            'numeric',
            'exists:books,id',
        ];
    }

    /**
     * Get the validation rules that apply to the title field.
     */
    public function getTitleRules(): array
    {
        return [
            'bail',
            'required',
            'string',
            'min:3',
            'max: 500',
        ];
    }

    /**
     * Get the validation rules that apply to the author field.
     */
    public function getAuthorRules(): array
    {
        return [
            'bail',
            'required',
            'string',
            'min:3',
            'max:150',
        ];
    }

    /**
     * Get the validation rules that apply to the available_copies field.
     */
    public function getAvailableCopiesRules(): array
    {
        return [
            'bail',
            'required',
            'integer',
            'numeric',
            'min:0',
        ];
    }
}

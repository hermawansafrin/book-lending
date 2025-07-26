<?php

namespace App\Http\Requests\Book;

use App\Http\Requests\APIRequest;
use App\Repositories\Auth\AuthRepository;
use App\Rules\BookHasAvailableCopies;
use App\Rules\PreventDuplicateBookLending;

class ApiLendRequest extends APIRequest
{
    use RuleTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return app(AuthRepository::class)->isLogin();
    }

    /**
     * Prepare the data for validation.
     */
    public function prepareForValidation(): void
    {
        $this->merge([
            'book_id' => (int) $this->route('id'),
            'user_id' => app(AuthRepository::class)->getUserId(),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'book_id' => $this->getBookLendingRules(),
            'user_id' => [], // only used for data fulfillment purposes
        ];
    }

    /**
     * Get the validation rules that apply to the book_id field for lending.
     */
    private function getBookLendingRules(): array
    {
        $rules = $this->getBookIdRules(); // default rules book
        $rules[] = new PreventDuplicateBookLending($this->user_id);
        $rules[] = new BookHasAvailableCopies;

        return $rules;
    }
}

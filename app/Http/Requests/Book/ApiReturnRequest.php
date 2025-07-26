<?php

namespace App\Http\Requests\Book;

use App\Http\Requests\APIRequest;
use App\Repositories\Auth\AuthRepository;
use App\Repositories\Loan\LoanRepository;
use App\Rules\BookIsLentToUser;

class ApiReturnRequest extends APIRequest
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
     * Merge the return_date field with the current date.
     */
    public function passedValidation(): void
    {
        $id = app(LoanRepository::class)->getLastOnGoingLoanId($this->book_id, $this->user_id);

        $this->merge([
            'id' => $id,
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
            'book_id' => $this->getBookReturnRules(),
            'user_id' => [], // only used for data fulfillment purposes
        ];
    }

    /**
     * Get the validation rules that apply to the book_id field for returning.
     */
    private function getBookReturnRules(): array
    {
        $rules = $this->getBookIdRules(); // default rules book
        $rules[] = new BookIsLentToUser($this->user_id); // must have on going loan

        return $rules;
    }
}

<?php

namespace App\Http\Requests\Book;

use App\Http\Requests\APIRequest;
use App\Repositories\Auth\AuthRepository;
use App\Rules\CannotDuplicateBookByTitleAndAuthor;

class ApiCreateRequest extends APIRequest
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
    protected function prepareForValidation(): void
    {
        $this->merge([
            // 'isbn' => $this->isbn, // TODO: doing with ISBN
            'check_exists' => true, // only additional data for checking title and author name
            'title' => $this->title,
            'author' => $this->author,
            'available_copies' => empty($this->available_copies) ? 0 : (int) $this->available_copies,
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
            'title' => $this->getTitleRules(),
            'author' => $this->getAuthorRules(),
            'available_copies' => $this->getAvailableCopiesRules(),
            'check_exists' => $this->getCheckExistsRules(),
        ];
    }

    /**
     * Get the validation rules that apply to the check_exists field.
     */
    private function getCheckExistsRules(): array
    {
        return [
            'bail',
            new CannotDuplicateBookByTitleAndAuthor($this->title, $this->author),
        ];
    }
}

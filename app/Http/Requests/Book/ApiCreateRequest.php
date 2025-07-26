<?php

namespace App\Http\Requests\Book;

use App\Http\Requests\APIRequest;
use App\Repositories\API\OpenLibrary\Getter;
use App\Repositories\Auth\AuthRepository;
use App\Rules\CannotDuplicateBookByTitleAndAuthor;
use Illuminate\Validation\ValidationException;

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
        $title = $this->title;
        $author = $this->author;

        if (! empty($this->isbn)) {
            $title = null; // cause use isbn, title must be reset
            $author = null; // cause use isbn, author must be reset
            $bookByIsbn = app(Getter::class)->getBookByIsbn($this->isbn, true);
            if (empty($bookByIsbn['data'])) {
                throw new ValidationException(
                    validator([], []), // only for init data validator
                    $this->sendError(__('validation.book.isbn_not_found'), 400)
                );
            }

            $title = $bookByIsbn['data']['title'];
            $author = $bookByIsbn['data']['author'];
        }

        $this->merge([
            'isbn' => $this->isbn ?? null,
            'check_exists' => true, // only additional data for checking title and author name
            'title' => $title,
            'author' => $author,
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
            'isbn' => $this->getIsbnRules(),
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

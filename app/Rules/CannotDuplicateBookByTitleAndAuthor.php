<?php

namespace App\Rules;

use App\Repositories\Book\BookRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CannotDuplicateBookByTitleAndAuthor implements ValidationRule
{
    private $title;

    private $author;

    public function __construct($title, $author)
    {
        $this->title = $title;
        $this->author = $author;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $isExists = app(BookRepository::class)->getIsTitleAndAuthorExists($this->title, $this->author);

        if ($isExists) {
            $fail(__('validation.book.book_title_and_author_already_exists'));
        }
    }
}

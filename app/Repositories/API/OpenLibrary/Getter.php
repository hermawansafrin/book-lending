<?php

namespace App\Repositories\API\OpenLibrary;

use App\Repositories\API\BaseAPIRepository;

class Getter extends BaseAPIRepository
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->baseUrl = 'https://openlibrary.org/api/'; // because its open api, its ok set the url here
    }

    /**
     * Get book by isbn
     */
    public function getBookByIsbn(string $isbn, bool $withFormatter = false): array
    {
        $response = $this->getData(
            endpoint: 'books',
            payload: [
                'bibkeys' => 'ISBN:'.$isbn,
                'format' => 'json',
                'jscmd' => 'data',
            ],
            withVerify: false,
            options: []
        );

        if ($withFormatter) {
            $response = Formatter::formatBook($response, $isbn);
        }

        return $response;
    }
}

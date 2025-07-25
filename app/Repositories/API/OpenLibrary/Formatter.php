<?php

namespace App\Repositories\API\OpenLibrary;

class Formatter
{
    /**
     * Format book data for app needs
     */
    public static function formatBook(array $response, string $isbn): array
    {
        // Return response as is if no ISBN data found
        if (empty($response['data'])) {
            return $response;
        }

        $book = $response['data']['ISBN:'.$isbn];

        $response['data'] = [
            'title' => $book['title'],
            'author' => $book['authors'][0]['name'] ?? 'N/A', // cause on db is 1 book 1 author, only get first author
        ];

        return $response;
    }
}

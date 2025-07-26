<?php

namespace Tests\Feature\Book;

use App\Models\User;
use App\Repositories\API\OpenLibrary\Getter;
use Illuminate\Support\Facades\DB;

/**
 * Test for book creation (must as admin user)
 */
class BookCreateTest extends BookTestCase
{
    private static bool $initialized = false;

    /**
     * Setup
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (! self::$initialized) {
            self::$initialized = true;
            DB::table('books')->truncate(); // make sure book its empty
            DB::table('users')->truncate(); // make sure user its empty
            $this->makeSureOneAdminAndOneMember(); // setup admin user for testing
        }
    }

    /**
     * Test user can create book with title and author
     */
    public function test_user_can_create_book_with_title_and_author()
    {
        $payload = [
            'title' => 'Test Book Title',
            'author' => 'Test Author',
            'available_copies' => 5,
        ];

        $response = $this->postData('books', $payload, true, User::find(1));

        $this->assertArrayHasKey('id', $response);
        $this->assertEquals($payload['title'], $response['title']);
        $this->assertEquals($payload['author'], $response['author']);
        $this->assertEquals($payload['available_copies'], $response['available_copies']);
    }

    /**
     * Test user can create book with isbn mocked openlibrary
     */
    public function test_user_can_create_book_with_isbn_mocked_openlibrary()
    {
        // Mock OpenLibrary Getter response
        $mockResponse = [
            'data' => [
                'ISBN:9781234567890' => [
                    'title' => 'Mocked Book Title from OpenLibrary',
                    'authors' => [
                        ['name' => 'Mocked Author Name'],
                    ],
                ],
            ],
        ];

        // Mock the OpenLibrary Getter
        $this->mock(Getter::class, function ($mock) {
            $mock->shouldReceive('getBookByIsbn')
                ->with('9781234567890', true)
                ->once()
                ->andReturn([
                    'data' => [
                        'title' => 'Mocked Book Title from OpenLibrary',
                        'author' => 'Mocked Author Name',
                    ],
                ]);
        });

        $payload = [
            'isbn' => '9781234567890',
            'available_copies' => 3,
        ];

        $response = $this->postData('books', $payload, true, User::find(1));

        // Verify the book was created with data from mocked OpenLibrary
        $this->assertArrayHasKey('id', $response);
        $this->assertEquals('Mocked Book Title from OpenLibrary', $response['title']);
        $this->assertEquals('Mocked Author Name', $response['author']);
        $this->assertEquals(3, $response['available_copies']);
    }
}

<?php

namespace Tests\Feature\Book;

use App\Models\User;
use Database\Seeders\BookSeeder;
use Illuminate\Support\Facades\DB;

/**
 * Test for book list
 * - this test its very dependent with BookSeeder
 */
class BookListTest extends BookTestCase
{
    private static bool $initialized = false;

    protected function setUp(): void
    {
        parent::setUp();

        if (! self::$initialized) {
            self::$initialized = true;
            DB::table('books')->truncate(); // make sure db empty for makesure books data same with seeder
            $this->makeSureOneAdminAndOneMember(); // has truncated data
            $this->seedBook(); // has seed data for sure
        }
    }

    /**
     * check book list can be access by admin
     */
    public function test_book_list_success_with_admin(): void
    {
        $response = $this->getData('books', [], true, User::find(1)); // acting as admin (Default input)
        $this->assertCheck($response);
    }

    /**
     * check book list can be access by admin
     */
    public function test_book_list_success_with_member(): void
    {
        $response = $this->getData('books', [], true, User::find(2)); // acting as member (Default input)
        $this->assertCheck($response);
    }

    /**
     * check book list can be access by admin
     */
    public function test_book_list_success_with_unauthenticated_user(): void
    {
        $response = $this->getData('books', [], true, null);
        $this->assertCheck($response);
    }

    /**
     * check book list with filter
     */
    public function test_book_list_with_filter_search(): void
    {
        $stringToCheck = 'Harry';
        $response = $this->getData('books', ['search' => $stringToCheck]);
        $this->assertCheck($response);
        // harry potter book only 1, so total data only 1
        $this->assertEquals(1, $response['total']);
        // check if string exists in title (case insensitive)
        $this->assertTrue(
            str_contains(
                strtolower($response['data'][0]['title']),
                strtolower($stringToCheck)
            )
        );
    }

    /**
     * check book list with filter per page
     */
    public function test_book_list_with_filter_per_page(): void
    {
        $response = $this->getData('books', ['per_page' => 1]);
        $this->assertCheck($response);
        $this->assertEquals(BookSeeder::BOOKS_COUNT, $response['total']);
        $this->assertEquals(1, $response['per_page']);
    }

    /**
     * check book list with filter page 2 and per page 1
     */
    public function test_book_list_with_filter_page_and_per_page(): void
    {
        $response = $this->getData('books', ['page' => 2, 'per_page' => 1]);
        $this->assertCheck($response);
        $this->assertEquals(BookSeeder::BOOKS_COUNT, $response['total']);
        $this->assertEquals(1, $response['per_page']);
        $this->assertEquals(2, $response['current_page']);
    }

    /**
     * check book list with sort by available copies asc and desc
     */
    public function test_book_list_with_sort_by_available_copies_asc_and_desc(): void
    {
        $theHobbit = DB::table('books')->whereRaw('title LIKE ?', ['%The Hobbit%'])->update(['available_copies' => 1]); // least amount
        $laskarPelangi = DB::table('books')->whereRaw('title LIKE ?', ['%Laskar Pelangi%'])->update(['available_copies' => 1000]); // most amount

        // the hobbit will the least available copies
        $response = $this->getData('books', ['sort_by' => 'available_copies', 'order_by' => 'asc']);
        $this->assertCheck($response);
        $this->assertEquals(BookSeeder::BOOKS_COUNT, $response['total']);
        $firstBook = $response['data'][0];
        $this->assertEquals('The Hobbit', $firstBook['title']);
        $this->assertEquals(1, $firstBook['available_copies']);

        // laskar pelangi will the most available copies
        $response = $this->getData('books', ['sort_by' => 'available_copies', 'order_by' => 'desc']);
        $this->assertCheck($response);
        $this->assertEquals(BookSeeder::BOOKS_COUNT, $response['total']);
        $firstBook = $response['data'][0];
        $this->assertEquals('Laskar Pelangi', $firstBook['title']);
        $this->assertEquals(1000, $firstBook['available_copies']);
    }

    /**
     * Assert check response from endpoint
     *  - structure
     *  - status code
     */
    private function assertCheck(array $response): void
    {
        /**
         * Check structure
         */
        $this->response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'current_page',
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'author',
                        'available_copies',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ],

        ]);

        $this->response->assertStatus(200);
    }
}

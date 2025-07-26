<?php

namespace Tests\Feature\Book;

use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Test for book list validation query parameter
 */
class BookListValidationTest extends BookTestCase
{
    private static bool $initialized = false;

    /**
     * Setup test
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (! self::$initialized) {
            self::$initialized = true;
            DB::table('books')->truncate(); // make sure db empty for makesure books data same with seeder
            $this->makeSureOneAdminAndOneMember(); // has truncated data
            $this->seedBook(); // has seed data for sure
        }

        $this->dontValidateCall = true;
    }

    /**
     * Input provider
     */
    public static function inputProvider(): array
    {
        return [
            'per_page.min' => [
                ['per_page' => -1],
                'min.numeric',
                ['attribute' => 'per page', 'min' => 1],
            ],
            'per_page.max' => [
                ['per_page' => 1000000],
                'max.numeric',
                ['attribute' => 'per page', 'max' => 100],
            ],

            'page.min' => [
                ['page' => -1],
                'min.numeric',
                ['attribute' => 'page', 'min' => 1],
            ],

            'search.string' => [
                ['search' => ['data' => 'test']],
                'string',
                ['attribute' => 'search'],
            ],
            'search.max' => [
                ['search' => str_repeat('a', 300)],
                'max.string',
                ['attribute' => 'search', 'max' => 255],
            ],

            'sort_by.string' => [
                ['sort_by' => ['data' => 'test']],
                'string',
                ['attribute' => 'sort by'],
            ],
            'sort_by.in' => [
                ['sort_by' => 'invalid'],
                'in',
                ['attribute' => 'sort by'],
            ],

            'order_by.string' => [
                ['order_by' => ['data' => 'test']],
                'string',
                ['attribute' => 'order by'],
            ],

            'order_by.in' => [
                ['order_by' => 'invalid'],
                'in',
                ['attribute' => 'order by'],
            ],
        ];
    }

    /**
     * Test book list validation query parameter
     */
    #[DataProvider('inputProvider')]
    public function test_book_list_validation_query_parameter(array $input, string $rule, array $param = []): void
    {
        $response = $this->getData('books', $input);
        $this->response->assertStatus(400);
        $message = __('validation.'.$rule, $param);
        $this->assertEquals($message, $response['message']);
    }
}

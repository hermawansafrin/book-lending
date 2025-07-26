<?php

namespace Tests\Feature\Book;

use App\Models\User;
use App\Repositories\API\OpenLibrary\Getter;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Test for book create validation
 */
class BookCreateValidationTest extends BookTestCase
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
            $this->makeSureOneAdminAndOneMember();
            DB::table('books')->truncate(); // make sure db empty
        }

        $this->dontValidateCall = true; // for passed bad request endpoint, and assert the message
    }

    /**
     * Input provider
     */
    public static function inputProvider(): array
    {
        return [
            'title.required_without' => [
                ['title' => null],
                'required_without',
                ['attribute' => 'title', 'values' => 'isbn'],
            ],
            'title.string' => [
                ['title' => ['data' => 'test']],
                'string',
                ['attribute' => 'title'],
            ],
            'title.min' => [
                ['title' => 'a'],
                'min.string',
                ['attribute' => 'title', 'min' => 3],
            ],
            'title.max' => [
                ['title' => str_repeat('a', 600)],
                'max.string',
                ['attribute' => 'title', 'max' => 500],
            ],

            'author.required_without' => [
                ['author' => null],
                'required_without',
                ['attribute' => 'author', 'values' => 'isbn'],
            ],
            'author.string' => [
                ['author' => ['data' => 'test']],
                'string',
                ['attribute' => 'author'],
            ],
            'author.min' => [
                ['author' => 'a'],
                'min.string',
                ['attribute' => 'author', 'min' => 3],
            ],
            'author.max' => [
                ['author' => str_repeat('a', 200)],
                'max.string',
                ['attribute' => 'author', 'max' => 150],
            ],

            'available_copies.min' => [
                ['available_copies' => -1],
                'min.numeric',
                ['attribute' => 'available copies', 'min' => 0],
            ],

            'isbn.required_without' => [
                ['isbn' => null, 'title' => null, 'author' => null],
                'required_without',
                ['attribute' => 'title', 'values' => 'isbn'],
            ],
            'isbn.book.isbn_not_found' => [
                [
                    'title' => null, 'author' => null,
                    'isbn' => '232423432423432324234',
                ],
                'book.isbn_not_found',
                [],
            ],

            'book_title_and_author_already_exists' => [
                [
                    'title' => 'Some Book',
                    'author' => 'Some Author',
                    'region_lang' => 'validation.book.',
                    'response_code' => 400,
                ],
                'book_title_and_author_already_exists',
                [],
                function ($testInstance) {
                    // make some title and author same
                    DB::table('books')->insert([
                        'title' => 'Some Book',
                        'author' => 'Some Author',
                        'available_copies' => 10,
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                    ]);
                },
            ],

            'only_admin_can_do_this_action' => [
                ['response_code' => 403, 'user_id' => 2, 'region_lang' => 'messages.auth.'],
                'only_admin_can_do_this_action',
                [],
            ],
        ];
    }

    /**
     * Test book create validation
     */
    #[DataProvider('inputProvider')]
    public function test_book_create_validation(array $input, string $rule, array $param = [], ?\Closure $before = null): void
    {

        $defaultInput = [
            'title' => 'Some Book',
            'author' => 'Some Author',
            'available_copies' => 10,
            'isbn' => null,
        ];

        $actingAs = User::find($input['user_id'] ?? 1); // default acting as admin
        $input = array_merge($defaultInput, $input);
        unset($input['user_id']);

        if ($before) {
            $before($this);
        }

        if ($input['isbn'] !== null && $rule === 'book.isbn_not_found') {
            // mock HTTP response for ISBN from open LIB
            $this->mock(Getter::class, function ($mock) {
                $mock->shouldReceive('getBookByIsbn')->andReturn([
                    'success' => false,
                    'data' => null,
                ]);
            });
        }

        $response = $this->postData('books', $input, true, $actingAs);
        $this->response->assertStatus($input['response_code'] ?? 400);

        $this->assertFalse($response['success']);
        $this->assertNull($response['data']);

        $regionLang = $input['region_lang'] ?? 'validation.';

        $message = __($regionLang.$rule, $param);
        $this->assertEquals($message, $response['message']);
    }
}

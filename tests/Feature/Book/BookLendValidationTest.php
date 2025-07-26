<?php

namespace Tests\Feature\Book;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Test for book lend validation
 */
class BookLendValidationTest extends BookTestCase
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
            DB::table('books')->truncate();
            DB::table('loans')->truncate();
            $this->makeSureOneAdminAndOneMember();
            $this->seedBook();
        }

        $this->dontValidateCall = true; // for passed bad request endpoint, and assert the message
    }

    /**
     * Input provider
     */
    public static function inputProvider(): array
    {
        return [
            'book_id.exists' => [
                ['id' => 100, 'user_id' => 1],
                'exists',
                ['attribute' => 'book id'],
            ],
            'book_id.no_available_copies' => [
                ['id' => 1, 'user_id' => 1],
                'book.no_available_copies',
                [],
                function ($testInstance) {
                    // make sure book has no available copies
                    DB::table('books')->where('id', 1)->update(['available_copies' => 0]);
                },
            ],
            'book.already_borrowed' => [
                ['id' => 1, 'user_id' => 1],
                'book.already_borrowed',
                [],
                function ($testInstance) {
                    // make sure book has been borrowed
                    DB::table('loans')->insert([
                        'book_id' => 1,
                        'user_id' => 1,
                        'loan_date' => now()->toDateTimeString(),
                        'return_date' => null,
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                    ]);
                },
            ],
            'user not authenticated' => [
                ['id' => 1, 'user_id' => null, 'region_lang' => 'messages'],
                'auth.unauthenticated',
                [],
            ],
        ];
    }

    /**
     * Test book lend validation as admin
     */
    #[DataProvider('inputProvider')]
    public function test_book_lend_validation_as_admin(array $input, string $rule, array $param = [], ?\Closure $before = null)
    {
        $id = $input['id'] ?? 1;
        $userId = $input['user_id'];
        $user = User::find($userId);

        if ($before) {
            $before($this);
        }

        $endpoint = 'books/'.$id.'/lend';
        $response = $this->postData($endpoint, $input, true, $user);

        $regionLang = $input['region_lang'] ?? 'validation';
        $messages = __("{$regionLang}.{$rule}", $param);

        $this->assertSame($messages, $response['message']);
    }
}

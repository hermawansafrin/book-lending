<?php

namespace Tests\Feature\Book;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Test for book return validation
 */
class BookReturnValidationTest extends BookTestCase
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

            $bookIds = [1, 2];
            $userIds = [1, 2];

            foreach ($bookIds as $bookId) {
                foreach ($userIds as $userId) {
                    $this->makeLoanBook($bookId, $userId);
                }
            }

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
            'book_id.not_borrowed' => [
                ['id' => 3, 'user_id' => 1],
                'book.not_borrowed',
                [],
            ],
            'user not authenticated' => [
                ['id' => 1, 'user_id' => null, 'region_lang' => 'messages'],
                'auth.unauthenticated',
                [],
            ],
        ];
    }

    /**
     * Test book return validation as admin
     */
    #[DataProvider('inputProvider')]
    public function test_book_return_validation_as_admin(array $input, string $rule, array $param = [])
    {
        $id = $input['id'] ?? 1;
        $userId = $input['user_id'];
        $user = User::find($userId);

        $endpoint = 'books/'.$id.'/return';
        $response = $this->postData($endpoint, $input, true, $user);

        $regionLang = $input['region_lang'] ?? 'validation';
        $messages = __("{$regionLang}.{$rule}", $param);

        $this->assertSame($messages, $response['message']);
    }
}

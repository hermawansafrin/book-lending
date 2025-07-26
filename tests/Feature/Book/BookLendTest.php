<?php

namespace Tests\Feature\Book;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Test for book lend (must as admin or member user - authenticated user)
 */
class BookLendTest extends BookTestCase
{
    private static bool $initialized = false;

    private static int $copiesBook1 = 10;

    private static int $copiesBook2 = 10;

    /**
     * Setup
     */
    protected function setUp(): void
    {
        parent::setUp();
        if (! self::$initialized) {
            self::$initialized = true;
            $this->makeSureOneAdminAndOneMember();
            // make sure book and loan empty
            DB::table('loans')->truncate();
            DB::table('books')->truncate();
            $this->seedBook(); // seed book

            // make sure book only have 10 copies
            DB::table('books')->whereIntegerInRaw('id', [1, 2])->update(['available_copies' => 10]);
        }
    }

    /**
     * Input provider
     */
    public static function inputProvider(): array
    {
        return [
            'book 1' => [
                ['id' => 1],
            ],
            'book 2' => [
                ['id' => 2],
            ],
        ];
    }

    /**
     * Test user can lend book
     */
    #[DataProvider('inputProvider')]
    public function test_user_can_lend_book_as_admin(array $input)
    {
        $endpoint = 'books/'.$input['id'].'/lend';

        // check available copies has same before lend the book
        $this->assertSame(
            $input['id'] === 1 ? self::$copiesBook1 : self::$copiesBook2,
            $this->getAvailableCopies($input['id'])
        );

        $response = $this->postData($endpoint, [], true, User::find(1));

        // check available copies has been decreased
        if ($input['id'] === 1) {
            self::$copiesBook1--;
        } else {
            self::$copiesBook2--;
        }

        $this->assertSame(
            $input['id'] === 1 ? self::$copiesBook1 : self::$copiesBook2,
            $this->getAvailableCopies($input['id'])
        );

        $this->assertCheck($response, $input['id'], 1);
    }

    /**
     * Test user can lend book as member
     */
    #[DataProvider('inputProvider')]
    public function test_user_can_lend_book_as_member(array $input)
    {
        $endpoint = 'books/'.$input['id'].'/lend';

        // check available copies has same before lend the book
        $this->assertSame(
            $input['id'] === 1 ? self::$copiesBook1 : self::$copiesBook2,
            $this->getAvailableCopies($input['id'])
        );

        $response = $this->postData($endpoint, [], true, User::find(2));

        // check available copies has been decreased
        if ($input['id'] === 1) {
            self::$copiesBook1--;
        } else {
            self::$copiesBook2--;
        }

        $this->assertSame(
            $input['id'] === 1 ? self::$copiesBook1 : self::$copiesBook2,
            $this->getAvailableCopies($input['id'])
        );

        $this->assertCheck($response, $input['id'], 2);
    }

    /**
     * Assert check when book has been lend to user
     */
    private function assertCheck(array $response, int $bookId, int $userId): void
    {
        $userHasLoanBook = DB::table('loans')
            ->whereRaw('book_id = ? AND user_id = ? AND loan_date IS NOT NULL AND return_date IS NULL', [
                $bookId, $userId,
            ])->exists();

        $this->assertTrue($userHasLoanBook);

        $this->assertSame(
            $bookId,
            $response['book_id']
        );
        $this->assertSame(
            $userId,
            $response['user_id']
        );
        $this->assertSame(now()->toDateString(), $response['loan_date']);
        $this->assertNull($response['return_date']);
    }
}

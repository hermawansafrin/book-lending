<?php

namespace Tests\Feature\Book;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Test for book return (must as admin or member user - authenticated user)
 */
class BookReturnTest extends BookTestCase
{
    private static bool $initialized = false;

    private static int $copiesBook1 = 8;

    private static int $copiesBook2 = 8;

    /**
     * Setup
     */
    protected function setUp(): void
    {
        parent::setUp();
        if (! self::$initialized) {
            self::$initialized = true;
            $this->makeSureOneAdminAndOneMember();
            DB::table('loans')->truncate();
            DB::table('books')->truncate();
            $this->seedBook();

            $bookIds = [1, 2];
            $userIds = [1, 2];

            foreach ($bookIds as $bookId) {
                foreach ($userIds as $userId) {
                    $this->makeLoanBook($bookId, $userId);
                }
            }

            // make sure book has been borrowed 2 (with first 10 book)
            DB::table('books')->whereIntegerInRaw('id', $bookIds)->update(['available_copies' => 8]);
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
     * Test user can return book
     */
    #[DataProvider('inputProvider')]
    public function test_user_can_return_book_as_admin(array $input)
    {
        $endpoint = 'books/'.$input['id'].'/return';

        // check available copies has same before lend the book
        $this->assertSame(
            $input['id'] === 1 ? self::$copiesBook1 : self::$copiesBook2,
            $this->getAvailableCopies($input['id'])
        );

        $response = $this->postData($endpoint, [], true, User::find(1));

        // check available copies has been increased
        if ($input['id'] === 1) {
            self::$copiesBook1++;
        } else {
            self::$copiesBook2++;
        }

        $this->assertSame(
            $input['id'] === 1 ? self::$copiesBook1 : self::$copiesBook2,
            $this->getAvailableCopies($input['id'])
        );

        $this->assertCheck($response, $input['id'], 1);
    }

    /**
     * Test user can return book as member
     */
    #[DataProvider('inputProvider')]
    public function test_user_can_return_book_as_member(array $input)
    {
        $endpoint = 'books/'.$input['id'].'/return';

        // check available copies has same before lend the book
        $this->assertSame(
            $input['id'] === 1 ? self::$copiesBook1 : self::$copiesBook2,
            $this->getAvailableCopies($input['id'])
        );

        $response = $this->postData($endpoint, [], true, User::find(2));

        // check available copies has been increased
        if ($input['id'] === 1) {
            self::$copiesBook1++;
        } else {
            self::$copiesBook2++;
        }

        $this->assertSame(
            $input['id'] === 1 ? self::$copiesBook1 : self::$copiesBook2,
            $this->getAvailableCopies($input['id'])
        );

        $this->assertCheck($response, $input['id'], 2);
    }

    /**
     * Assert check when book has been returned by user
     */
    private function assertCheck(array $response, int $bookId, int $userId): void
    {
        $userHasOnGoingLoan = DB::table('loans')
            ->whereRaw('book_id = ? AND user_id = ? AND loan_date IS NOT NULL AND return_date IS NULL', [
                $bookId, $userId,
            ])->exists();

        $this->assertFalse($userHasOnGoingLoan);

        $this->assertSame(
            $bookId,
            $response['book_id']
        );
        $this->assertSame(
            $userId,
            $response['user_id']
        );
        $this->assertSame(now()->toDateString(), $response['return_date']);
        $this->assertSame(now()->toDateString(), $response['loan_date']);
    }
}

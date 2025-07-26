<?php

namespace Tests\Feature\User;

use App\Models\User;

/**
 * Test for user make admin (from member to admin)
 */
class UserMakeAdminTest extends UserTestCase
{
    private static bool $initialized = false;

    /**
     * Setup
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (! self::$initialized) { // make sure only run once
            self::$initialized = true;
            $this->makeSureOneAdminAndOneMember();
        }
    }

    /**
     * Test user can make admin
     */
    public function test_make_admin()
    {
        $userId = 2;
        $endpoint = 'users/'.$userId.'/make-admin';
        $user = User::find(1);

        $response = $this->postData($endpoint, [], true, $user);

        $this->assertSame($userId, $response['id']); // make sure it is the same user
        $this->assertSame(1, $response['is_admin']);

        // after user id 2 from member to admin
        // user must be can hit protected api (example: list user)
        $endpoint = 'users';
        $user2 = User::find(2);
        $response = $this->getData($endpoint, [], true, $user2);

        $this->assertSame(200, $this->response->status());
    }
}

<?php

namespace Tests\Feature\Authentication;

use Illuminate\Support\Facades\DB;

/**
 * Test for check for registerred user success
 */
class AuthenticationRegisterTest extends AuthenticationTestCase
{
    private static bool $initialized = false;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (! self::$initialized) {
            self::$initialized = true;
            DB::table('users')->truncate(); // make sure user its empty
        }
    }

    /**
     * Test register user 1
     */
    public function test_register_1(): void
    {
        $input = [
            'name' => 'user 1',
            'email' => 'user1@mail.test',
            'password' => 'somePassword99!',
            'password_confirmation' => 'somePassword99!',
        ];

        $this->assertCountUser(0); // make sure user is empty
        $response = $this->postData('register', $input);
        $this->assertCountUser(1); // make sure user is created

        $this->assertCheck($response, $input);
    }

    /**
     * Test register user 2
     */
    public function test_register_2(): void
    {
        $input = [
            'name' => 'user 2',
            'email' => 'user2@mail.test',
            'password' => 'somePassword99!',
            'password_confirmation' => 'somePassword99!',
        ];

        $this->assertCountUser(1); // make sure user is created (by test 1)
        $response = $this->postData('register', $input);
        $this->assertCountUser(2); // make sure user is created (by test 2)

        $this->assertCheck($response, $input);
    }

    /**
     * Check for created user match with payload
     */
    private function assertCheck(array $response, array $input): void
    {
        $keys = ['name', 'email'];

        foreach ($keys as $key) {
            $this->assertEquals($input[$key], $response[$key]);
        }

        $this->assertSame(0, $response['is_admin']); // make sure user is not admin
    }
}

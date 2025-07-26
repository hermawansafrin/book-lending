<?php

namespace Tests\Feature\Authentication;

use Illuminate\Support\Facades\DB;

/**
 * Test for check for login is successfully and retrieve a bearer token
 */
class AuthenticationLoginTest extends AuthenticationTestCase
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

            $this->makeSureOneAdminAndOneMember();
        }
    }

    /**
     * Test login success as admin
     */
    public function test_login_success_admin(): void
    {
        $email = 'admin@mail.test';
        $password = 'somePassword99!';
        $response = $this->postData('login', [
            'email' => $email,
            'password' => $password,
        ]);

        $this->assertCheck($response, [
            'name' => 'admin',
            'email' => $email,
            'is_admin' => 1,
        ]);
    }

    /**
     * Test login success as member
     */
    public function test_login_success_not_admin(): void
    {
        $email = 'member@mail.test';
        $password = 'somePassword99!';
        $response = $this->postData('login', [
            'email' => $email,
            'password' => $password,
        ]);

        $this->assertCheck($response, [
            'name' => 'member 1',
            'email' => $email,
            'is_admin' => 0,
        ]);

        self::$databaseSetup = false; // make sure database is not fresh install for next test
    }

    /**
     * Assert check response
     */
    private function assertCheck(array $response, array $input): void
    {
        // assert response tructure
        $this->response->assertJsonStructure([
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                    'is_admin',
                    'created_at',
                    'updated_at',
                ],
                'token' => [
                    'type', 'value',
                ],
            ],
        ]);

        $matchUserKeys = ['name', 'email', 'is_admin'];

        foreach ($matchUserKeys as $key) {
            $this->assertEquals($input[$key], $response['user'][$key]);
        }
    }
}

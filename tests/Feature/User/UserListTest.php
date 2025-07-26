<?php

namespace Tests\Feature\User;

use App\Models\User;

class UserListTest extends UserTestCase
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
     * Test user can list users
     */
    public function test_list_user()
    {
        $endpoint = 'users';
        $response = $this->getData($endpoint, [], true, User::find(1));

        $this->response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'email_verified_at',
                        'is_admin',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links',
                'next_page_url',
                'path',
                'per_page',
                'to',
                'total',
            ],
        ]);
    }
}

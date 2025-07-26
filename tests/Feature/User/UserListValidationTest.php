<?php

namespace Tests\Feature\User;

use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;

class UserListValidationTest extends UserTestCase
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

        $this->dontValidateCall = true; // for passed bad request endpoint, and assert the message
    }

    public static function inputProvider(): array
    {
        return [
            'per_page.min' => [
                ['per_page' => -1, 'user_id' => 1],
                'min.numeric',
                ['attribute' => 'per page', 'min' => 1],
            ],
            'per_page.max' => [
                ['per_page' => 1000000, 'user_id' => 1],
                'max.numeric',
                ['attribute' => 'per page', 'max' => 100],
            ],

            'page.min' => [
                ['page' => -1, 'user_id' => 1],
                'min.numeric',
                ['attribute' => 'page', 'min' => 1],
            ],

            'only_admin_can_do_this_action' => [
                ['response_code' => 403, 'user_id' => 2, 'region_lang' => 'messages.auth'],
                'only_admin_can_do_this_action',
                [],
            ],
        ];
    }

    /**
     * Test user can list users validation
     */
    #[DataProvider('inputProvider')]
    public function test_list_user_validation(array $input, string $rule, array $param = [])
    {
        $endpoint = 'users';
        $userId = $input['user_id'];
        $user = User::find($userId);

        $response = $this->getData($endpoint, $input, true, $user);
        $this->response->assertStatus($input['response_code'] ?? 400);

        $regionLang = $input['region_lang'] ?? 'validation';
        $messages = __("{$regionLang}.{$rule}", $param);

        $this->assertSame($messages, $response['message']);
    }
}

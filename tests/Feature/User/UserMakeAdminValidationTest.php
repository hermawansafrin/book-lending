<?php

namespace Tests\Feature\User;

use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Test for user make admin validation (from member to admin)
 */
class UserMakeAdminValidationTest extends UserTestCase
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

    /**
     * Input provider
     */
    public static function inputProvider(): array
    {
        return [
            'id.exists' => [
                ['id' => 0, 'user_id' => 1],
                'exists',
                ['attribute' => 'id'],
            ],
            'id.user_already_as_admin' => [
                ['id' => 1, 'user_id' => 1],
                'user_already_as_admin',
                [],
            ],

            'only_admin_can_do_this_action' => [
                ['response_code' => 403, 'id' => 2, 'user_id' => 2, 'region_lang' => 'messages.auth'],
                'only_admin_can_do_this_action',
                [],
            ],
        ];
    }

    /**
     * Test user can make admin validation
     */
    #[DataProvider('inputProvider')]
    public function test_make_admin_validation(array $input, string $rule, array $param = [])
    {
        $userId = $input['id'];
        $endpoint = 'users/'.$userId.'/make-admin';

        $actingAsUserId = $input['user_id'];
        $actingAsUser = User::find($actingAsUserId);

        $response = $this->postData($endpoint, $input, true, $actingAsUser);
        $this->response->assertStatus($input['response_code'] ?? 400);

        $regionLang = $input['region_lang'] ?? 'validation';
        $messages = __("{$regionLang}.{$rule}", $param);

        $this->assertSame($messages, $response['message']);
    }
}

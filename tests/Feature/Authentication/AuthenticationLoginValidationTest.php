<?php

namespace Tests\Feature\Authentication;

use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AuthenticationLoginValidationTest extends TestCase
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

            // create user as admin
            $this->createUser([
                'name' => 'admin',
                'email' => 'admin@mail.test',
                'is_admin' => 1,
            ]);

        }

        $this->dontValidateCall = true; // for pass the assert successfull and get the response message validation
    }

    /**
     * Input provider
     */
    public static function inputProvider(): array
    {
        return [
            // email region
            'email.required' => [
                [
                    'email' => null,
                ],
                'required',
                ['attribute' => 'email'],
            ],
            'email.max' => [
                [
                    'email' => str_repeat('a', 99).'@mail.test',
                ],
                'max.string',
                ['attribute' => 'email', 'max' => 100],
            ],
            'email.email' => [
                [
                    'email' => 'not-email',
                ],
                'email',
                ['attribute' => 'email'],
            ],
            'email.exists' => [
                [
                    'email' => 'not-exists@mail.test',
                ],
                'exists',
                ['attribute' => 'email'],
            ],
            // password region
            'password.required' => [
                [
                    'password' => null,
                ],
                'required',
                ['attribute' => 'password'],
            ],
            'password.min' => [
                [
                    'password' => '123',
                ],
                'min.string',
                ['attribute' => 'password', 'min' => 6],
            ],
            'password.max' => [
                [
                    'password' => str_repeat('a', 99),
                ],
                'max.string',
                ['attribute' => 'password', 'max' => 16],
            ],
            'email_and_password_match.auth_failed' => [
                [
                    'email' => 'admin@mail.test',
                    'password' => 'falsePassword',
                    'response_code' => 401,
                    'special_validation_message' => 'auth',
                ],
                'failed',
                [],
            ],
        ];
    }

    /**
     * Test login validation
     */
    #[DataProvider('inputProvider')]
    public function test_login_validation(array $input, string $rule, array $param): void
    {
        $defaultInput = [
            'email' => 'admin@mail.test',
            'password' => 'somePassword99!',
        ];

        $input = array_merge($defaultInput, $input);

        $responseCode = 400;
        if (array_key_exists('response_code', $input)) {
            $responseCode = $input['response_code'];
            unset($input['response_code']);
        }

        $response = $this->postData('login', $input);

        $this->response->assertStatus($responseCode); // must be not valid

        $this->assertFalse($response['success']);
        $this->assertNull($response['data']);

        $this->response->assertJsonStructure([
            'success',
            'data',
            'message',
        ]);

        // check response message
        $langFile = $input['special_validation_message'] ?? 'validation';
        $message = __("{$langFile}.{$rule}", $param);

        $this->assertSame(
            $message,
            $response['message']
        );
    }
}

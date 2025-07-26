<?php

namespace Tests\Feature\Authentication;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;

class AuthenticationRegisterValidationTest extends AuthenticationTestCase
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
            self::$databaseSetup = false; // make sure data fresh
        }

        $this->dontValidateCall = true; // for pass the assert successfull and get the response message validation
    }

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
            'email.unique' => [
                [
                    'email' => 'exists@mail.test',
                ],
                'unique',
                ['attribute' => 'email'],
                function ($testInstance) {
                    // only for test
                    DB::table('users')->insert([
                        'name' => 'user 1',
                        'email' => 'exists@mail.test',
                        'password' => Hash::make('somePassword99!'),
                        'is_admin' => 0,
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                    ]);
                },
            ],

            // name region
            'name.required' => [
                [
                    'name' => null,
                ],
                'required',
                ['attribute' => 'name'],
            ],
            'name.max' => [
                [
                    'name' => str_repeat('a', 201),
                ],
                'max.string',
                ['attribute' => 'name', 'max' => 200],
            ],
            'name.min' => [
                [
                    'name' => str_repeat('a', 1),
                ],
                'min.string',
                ['attribute' => 'name', 'min' => 3],
            ],

            // password region (with confirmation)
            'password.required' => [
                [
                    'password' => null,
                ],
                'required',
                ['attribute' => 'password'],
            ],
            'password.min' => [
                [
                    'password' => 'some',
                ],
                'min.string',
                ['attribute' => 'password', 'min' => 6],
            ],
            'password.max' => [
                [
                    'password' => str_repeat('a', 256),
                ],
                'max.string',
                ['attribute' => 'password', 'max' => 16],
            ],
            'password.confirmed' => [
                [
                    'password' => 'somePassword99!',
                    'password_confirmation' => 'differentPassword99!',
                ],
                'confirmed',
                ['attribute' => 'password'],
            ],
            'password.password_format.string_must_at_least_one_uppercase' => [
                [
                    'password' => '1234567890',
                    'password_confirmation' => '1234567890',
                ],
                'string_must_at_least_one_uppercase',
                ['attribute' => 'password'],
            ],
            'password.password_format.string_must_have_one_number' => [
                [
                    'password' => 'SomePass',
                    'password_confirmation' => 'SomePass',
                ],
                'string_must_have_one_number',
                ['attribute' => 'password'],
            ],
            'password.password_format.string_must_at_least_one_special_char' => [
                [
                    'password' => 'SomePass99',
                    'password_confirmation' => 'SomePass99',
                ],
                'string_must_at_least_one_special_char',
                ['attribute' => 'password'],
            ],
        ];
    }

    /**
     * Test register validation
     */
    #[DataProvider('inputProvider')]
    public function test_register_validation(array $input, string $rule, array $param = [], ?\Closure $before = null): void
    {
        $defaultInput = [
            'name' => 'user 1',
            'email' => 'user1@mail.test',
            'password' => 'somePassword99!',
            'password_confirmation' => 'somePassword99!',
        ];

        if ($before) {
            $before($this);
        }

        $input = array_merge($defaultInput, $input);

        $response = $this->postData('register', $input);
        $this->response->assertStatus(400); // must be not valid
        // print_r($response);

        // check response returned data
        $this->assertFalse($response['success']);
        $this->assertNull($response['data']);
        // make sure validation message
        $message = __("validation.{$rule}", $param);

        $this->assertSame($message, $response['message']);
    }
}

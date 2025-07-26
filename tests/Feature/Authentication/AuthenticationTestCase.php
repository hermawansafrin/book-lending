<?php

namespace Tests\Feature\Authentication;

use Tests\TestCase;

abstract class AuthenticationTestCase extends TestCase
{
    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Assert count user
     */
    protected function assertCountUser(int $count): void
    {
        $this->assertDatabaseCount('users', $count);
    }
}

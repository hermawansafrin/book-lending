<?php

namespace Tests;

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;
use Ramsey\Uuid\Uuid;

abstract class TestCase extends BaseTestCase
{
    use APICallTestTrait;
    use SeederTestTrait;

    protected static bool $databaseSetup = false; // for manually created setup test

    protected bool $dontValidateCall = false; // for check validation messages

    protected string $baseUrl = 'api/';

    protected string $url = '';

    protected static ?User $user = null;

    protected ?TestResponse $response = null;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // makesure for using testing environment
        $app['env'] = 'testing';

        // make sure using testing database
        Config::set('database.connections.mysql.database', env('DB_DATABASE'));

        return $app;
    }

    /**
     * Constructor
     */
    public function __construct(string $name = '')
    {
        if (empty($name)) {
            $name = substr(str_repeat(Uuid::uuid4()->toString(), 32), 0, 255);
        }

        parent::__construct($name);
    }

    /**
     * Setup function test
     */
    protected function setUp(): void
    {
        parent::setUp();

        // make sure using testing database
        $this->ensureTestingDatabase();

        // setup database only once at the beginning of all tests (manual approach)
        if (! static::$databaseSetup) {
            print_r("\033[34m \ninitialize testing database (manual setup)\033[0m");
            $this->artisan('migrate:fresh');
            static::$databaseSetup = true;
        }
    }

    /**
     * Make sure using testing database
     */
    protected function ensureTestingDatabase(): void
    {
        // make sure using testing database
        $databaseName = DB::connection()->getDatabaseName();
        $expectedDatabase = env('DB_DATABASE');

        if ($databaseName !== $expectedDatabase) {
            throw new \Exception("Test is not using the correct testing database. Expected: {$expectedDatabase}, Actual: {$databaseName}");
        }

        // make sure environment is testing
        if (app()->environment() !== 'testing') {
            throw new \Exception('Test is not running in the testing environment. Current environment: '.app()->environment());
        }
    }

    /**
     * Clean up after each test
     */
    protected function tearDown(): void
    {
        // reset state after each test if needed
        $this->beforeApplicationDestroyed(function () {
            // cleanup logic if needed
        });

        parent::tearDown();
    }
}

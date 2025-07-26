<?php

namespace App\Console\Commands;

use Database\Seeders\BookSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FreshInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fresh-install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fresh install the application including databse, seeders, and other necessary files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->clearCache(); // make sure clear cache first
        $this->clearStorage(); // clear the storage
        $this->makeDatabaseFresh(); // make the database fresh
        $this->initialSeed(); // initial seed data
    }

    /**
     * Initial seed data for the application
     */
    private function initialSeed(): void
    {
        $this->comment('Seeding database..');

        $seeders = [
            UserSeeder::class, // for generate initial user datas
            BookSeeder::class, // for generate initial book datas
            // add here if there is anything else
        ];

        collect($seeders)->each(function ($seeder) {
            $this->seedingOutput($seeder);
            $this->callSilent('db:seed', ['--class' => $seeder]);
        });

        $this->info('Database seeded successfully');
        $this->newLine();
    }

    /**
     * Make the database fresh
     */
    private function makeDatabaseFresh(): void
    {
        $this->comment('Making database fresh...');
        $this->callSilent('migrate:fresh');
        $this->info('Database made fresh successfully');
        $this->newLine();
    }

    /**
     * Clear the storage
     */
    private function clearStorage(): void
    {
        $this->comment('Clearing storage...');

        $storages = [
            'public',
            // add here if there is anything else
        ];

        foreach ($storages as $storage) {
            $this->comment('Clearing '.$storage.' storage...');
            Storage::disk($storage)->deleteDirectory('/');
        }

        /** create symlink */
        $this->comment('Creating symlink...');
        $this->callSilent('storage:link');
        $this->info('Storage cleared successfully');

        $this->newLine();
    }

    /**
     * Clear all the cache from application
     */
    private function clearCache(): void
    {
        $this->comment('Clearing cache...');
        try {
            collect([
                'event:clear',
                'view:clear',
                'cache:clear',
                'config:clear',
                'config:cache',
                'route:clear',
            ])->each(function ($command) {
                $this->callSilent($command);
            });

            $this->info('Cache cleared successfully');
            $this->newLine();
        } catch (\Exception $e) {
            $this->error('Failed to clear cache: '.$e->getMessage());
        }
    }

    /**
     * Console output seeding class
     */
    private function seedingOutput(string $class): void
    {
        $this->line("<comment>Seeding : </comment> {$this->cyan($class)}");
    }

    /**
     * Formatting command style
     */
    private function cyan(string $message): string
    {
        return "<fg=cyan>{$message}</>";
    }
}

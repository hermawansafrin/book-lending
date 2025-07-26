<?php

namespace App\Console\Commands;

use App\Repositories\API\OpenLibrary\Getter;
use Illuminate\Console\Command;

class AppCheckISBN extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-isbn';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check ISBN from Open Library API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isbn = $this->ask('Enter ISBN:');

        $response = app(Getter::class)->getBookByIsbn($isbn, true);

        if (empty($response['data'])) {
            $this->error('ISBN not found');

            return;
        }

        $this->table(
            headers: ['Title', 'Author'],
            rows: [
                [$response['data']['title'], $response['data']['author']],
            ]
        );
    }
}

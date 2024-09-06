<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class Playground extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'playground';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A command to test something out';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Test something out...
    }
}

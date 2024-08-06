<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Restore extends SiteCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore a backup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        dump('restore...');
    }
}

<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Backup extends SiteCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        dump('backup...');
    }
}

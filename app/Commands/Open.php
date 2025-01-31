<?php

namespace App\Commands;

class Open extends SiteCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'open';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Open a site';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $site = $this->ask_user_for_site('Select a site to open');

        exec("open {$site->url('/wp-admin')}");
    }
}

<?php

namespace App\Commands;

use App\Domain\Site;

use function Laravel\Prompts\info;
use function Laravel\Prompts\select;

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

        exec("open http://{$site->slug()}.test/wp-admin");
    }
}

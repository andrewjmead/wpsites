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
        $config = $this->get_config();

        $sites_directory = $config->get_sites_directory();

        info('Checking which sites are WordPress sites...');

        $slugs = Site::get_all_slugs($sites_directory);

        if ($slugs->count() === 0) {
            info('There are no WordPress sites to open');
            exit(0);
        }

        $selected_slug = select(
            label: 'Which site would you link to open?',
            options: $slugs,
            scroll: 20,
        );

        exec("open http://{$selected_slug}.test/wp-admin");
    }
}

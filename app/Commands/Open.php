<?php

namespace App\Commands;

use App\Domain\Site;
use Illuminate\Support\Facades\File;

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

        $options = collect(File::directories($sites_directory))->filter(function ($directory) use ($sites_directory) {
            $site = new Site($sites_directory, basename($directory));
            [$success, $output] = $site->execute('wp core is-installed');

            return $success;
        })->map(function ($directory) {
            return basename($directory);
        });

        if ($options->count() === 0) {
            info('There are no WordPress sites to open');
            exit(0);
        }

        $selected_slug = select(
            label: 'Pick a site to open',
            options: array_values($options->toArray()),
        );

        exec("open http://{$selected_slug}.test/wp-admin");
    }
}

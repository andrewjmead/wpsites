<?php

namespace App\Commands;

use App\Domain\Site;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;

use LaravelZero\Framework\Commands\Command;

class Destroy extends SiteCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'destroy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Destroy a site';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $config = $this->get_config();

        $sites_directory = $config->get_sites_directory();

        info('Checking which sites are WordPress sites...');

        $options = collect(File::directories($sites_directory))->filter(function ($directory) use ($sites_directory) {
            $site               = new Site($sites_directory, basename($directory));
            [$success, $output] = $site->execute('wp core is-installed');

            return $success;
        })->map(function ($directory) {
            return basename($directory);
        });

        if ($options->count() === 0) {
            info('There are no WordPress sites to destroy');
            exit(0);
        }

        $selected_slugs = multiselect(
            label: 'Which sites would you like to destroy?',
            options: array_values($options->toArray()),
            scroll: 20,
            required: true,
        );
        $sites_term = Str::plural('site', count($selected_slugs));

        $confirmed = confirm(
            label: "Are you sure you want to destroy the {$sites_term} listed above?",
            default: false,
        );

        if (! $confirmed) {
            exit(0);
        }

        foreach ($selected_slugs as $slug) {
            info("Deleting site \"{$slug}\"");

            $site = new Site($sites_directory, $slug);

            $site->execute_alt('Dropping database...', 'wp db drop', [
                'yes' => true,
            ], true);

            File::deleteDirectory($site->folder_path());
        }

        exit(1);
    }
}

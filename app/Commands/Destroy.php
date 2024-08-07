<?php

namespace App\Commands;

use App\Domain\Site;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\select;

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
            $site = new Site($sites_directory, basename($directory));
            [$success, $output] = $site->execute('wp core is-installed');

            return $success;
        })->map(function ($directory) {
            return basename($directory);
        });

        if ($options->count() === 0) {
            info('There are no WordPress sites to destroy');
            exit(0);
        }

        $selected_slug = select(
            label: 'Which site would you like to destroy?',
            options: array_values($options->toArray()),
        );

        $confirmed = confirm(
            label: "Are you sure yuo want to destroy \"{$selected_slug}\"?",
            default: false,
            yes: 'Yes',
            no: 'No'
        );

        if (! $confirmed) {
            exit(0);
        }

        $site = new Site($sites_directory, $selected_slug);

        $site->execute_alt('Dropping database...', 'wp db drop', [
            'yes' => true,
        ]);

        info('Deleting site folder...');
        File::deleteDirectory($site->folder_path());
    }
}

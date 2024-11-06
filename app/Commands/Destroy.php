<?php

namespace App\Commands;

use App\Domain\Site;
use App\Domain\SiteOption;
use App\Domain\SiteOptions;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;

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
        $config           = $this->get_config();
        $site_directories = $config->get_site_directories();
        $sites            = Site::get_sites($site_directories);

        if ($sites->count() === 0) {
            info('There are no WordPress sites to destroy');
            exit(0);
        }

        $selected_options = multiselect(
            label: 'Select sites to destroy',
            options: SiteOptions::from($sites),
            scroll: 20,
            required: true,
            hint: 'Use the space bar to select options.',
        );
        $selected_sites = collect($selected_options)->map(function (SiteOption $option) {
            return $option->site();
        });
        $sites_string = Str::plural('site', $selected_sites->count());

        $confirmed = confirm(
            label: "Are you sure you want to destroy the {$sites_string} listed above?",
            default: false,
        );

        if (! $confirmed) {
            exit(0);
        }

        $selected_sites->each(function (Site $site) {
            info("Deleting site at \"{$site->directory()}\"");
            $site->destroy();
        });

        exit(0);
    }
}

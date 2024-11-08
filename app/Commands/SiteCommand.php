<?php

namespace App\Commands;

use App\Domain\ConfigFile;
use App\Domain\Site;
use App\Domain\SiteOption;

use function Laravel\Prompts\info;
use function Laravel\Prompts\select;

use LaravelZero\Framework\Commands\Command;

abstract class SiteCommand extends Command
{
    protected function ask_user_for_site(string $prompt): Site
    {
        $site_directories = ConfigFile::parse()->get_site_directories();
        $sites            = Site::get_sites($site_directories);

        if ($sites->isEmpty()) {
            info('There are no WordPress sites to open');
            exit(0);
        }

        // Laravel prompts returns the label value for options that pass array_is_list. This isn't
        // helpful. We offset all indexes by 1 to have it return the key (index + 1) instead.
        $options = $sites->mapWithKeys(function (Site $site, int $index) {
            return [ $index + 1 => new SiteOption($site) ];
        });

        $selected_site_index = select(
            label: $prompt,
            options: $options,
            scroll: 20,
        );

        // Reduce the index by 1 to account for increase in index above
        return $sites->get($selected_site_index - 1);
    }

    protected function is_valid_kebab_name(string $name): bool
    {
        return preg_match('/^[a-z0-9-]+$/', $name) === 1;
    }
}

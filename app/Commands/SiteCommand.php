<?php

namespace App\Commands;

use App\Domain\ConfigFile;
use App\Domain\ConfigTypes\Config;

use App\Domain\Site;
use function Laravel\Prompts\error;

use LaravelZero\Framework\Commands\Command;
use function Laravel\Prompts\info;
use function Laravel\Prompts\select;

abstract class SiteCommand extends Command
{
    protected function get_config(): Config
    {
        if (ConfigFile::exists()) {
            return ConfigFile::get_config();
        }

        error('Config file not found! Run `wpsites config` to get started.');
        exit(1);
    }

    protected function ask_user_for_site(string $prompt = 'Select a site'): Site
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
            label: $prompt,
            options: $slugs,
            scroll: 20,
        );

        return new Site($sites_directory, $selected_slug);

    }
}

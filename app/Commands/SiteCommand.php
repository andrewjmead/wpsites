<?php

namespace App\Commands;

use App\Domain\ConfigFile;
use App\Domain\ConfigTypes\Config;

use function Laravel\Prompts\error;

use LaravelZero\Framework\Commands\Command;

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
}

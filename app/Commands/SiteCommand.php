<?php

namespace App\Commands;

use App\Domain\ConfigFile;
use App\Domain\ConfigTypes\Config;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;

abstract class SiteCommand extends Command
{
    protected function get_config(): Config
    {
        if (ConfigFile::exists()) {
            return ConfigFile::get_config();
        }

        note('No configuration file found');
        note('Creating ' . ConfigFile::file_path());

        try {
            $bytesWritten = File::put(ConfigFile::file_path(), ConfigFile::default_configuration());

            if ($bytesWritten === false) {
                throw new \Exception('Unable to create configuration file');
            }
        } catch (\Exception $e) {
            error('Unable to create configuration file');
        }

        info('Configuration file created. Opening...');
        sleep(2);
        exec('open -e ' . ConfigFile::file_path());
        exit(0);
    }
}

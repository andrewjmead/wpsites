<?php

namespace App\Commands;

use App\Domain\ConfigFile;
use Illuminate\Support\Facades\File;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;

use LaravelZero\Framework\Commands\Command;

class Config extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configure your WPSites installation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (ConfigFile::exists()) {
            $confirmed = confirm(
                label: 'Config file already exists. Do you want to override it?',
                default: false,
            );

            if ($confirmed) {
                info('Removing existing config file');
                File::delete(ConfigFile::file_path());
            } else {
                exit(0);
            }
        }

        $path = ConfigFile::file_path();
        info("Copying default config to `{$path}`");

        try {
            $bytesWritten = File::put(ConfigFile::file_path(), ConfigFile::default_configuration());

            if ($bytesWritten === false) {
                throw new \Exception('Unable to create configuration file');
            }
        } catch (\Exception $e) {
            error("Unable to create configuration file! Please manually create {$path} using the following command:");

            note('curl -o ~/.wpsites.php https://raw.githubusercontent.com/andrewjmead/wpsites/main/config/wpsites.php');

            exit(1);
        }

        info('Config file successfully created!');
        exit(0);
    }
}

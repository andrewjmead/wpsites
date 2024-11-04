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
        $config_path = ConfigFile::file_path();

        note("Copying default config to `{$config_path}`");

        if (ConfigFile::exists()) {
            $confirmed = confirm(
                label: 'Config file already exists. Override it?',
                default: false,
            );

            if ($confirmed) {
                note('Deleting config file');
                File::delete($config_path);
                note("Copying default config to `{$config_path}`");
            } else {
                exit(0);
            }
        }

        try {
            $bytesWritten = File::put($config_path, ConfigFile::default_configuration());

            if ($bytesWritten === false) {
                throw new \Exception('Unable to create configuration file');
            }
        } catch (\Exception $e) {
            error("Unable to create configuration file! Please manually create {$config_path} using the following command:");

            note('curl -o ~/.wpsites.php https://raw.githubusercontent.com/andrewjmead/wpsites/main/config/wpsites.php');

            exit(1);
        }

        info('Config file successfully created!');
        note('Checkout the docs here to learn what you need to change: https://github.com/andrewjmead/wpsites#configure-your-sites-directory');

        exit(0);
    }
}

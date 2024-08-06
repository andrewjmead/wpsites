<?php

namespace App\Domain;

use App\Domain\ConfigTypes\Config;
use Illuminate\Support\Facades\File;
use function Laravel\Prompts\error;
use function Laravel\Prompts\note;

class ConfigFile
{
    public static function get_config(): Config
    {
        $config_file_path = ConfigFile::file_path();
        note("Loading config file at \"{$config_file_path}\"");

        $config_file_contents = require ConfigFile::file_path();

        try {
            $config = (new \CuyZ\Valinor\MapperBuilder())
                ->mapper()
                ->map(Config::class, \CuyZ\Valinor\Mapper\Source\Source::array($config_file_contents));

            // TODO - An alternative to this would be to parse the defaults, and then parse the templates
            //  and use a customized mapper to pass in the defaults to the constructor instead of using
            //  the set_defaults method.
            foreach ($config->templates as $template) {
                $template->set_defaults($config->defaults);
            }
        } catch (\CuyZ\Valinor\Mapper\MappingError $error) {
            error("Invalid configuration file!");
            error($error->getMessage());
            exit(1);
        } catch (\Throwable $error) {
            error("Invalid configuration file! Configuration file must be a PHP file that returns an associative array.");
            exit(1);
        }

        if(!File::isDirectory($config->get_sites_directory())) {
            error("The \"sites_directory\" in your configuration file does not exist! Unable to find \"{$config->get_sites_directory()}\"");
            exit(1);
        }

        return $config;
    }

    public static function exists(): bool
    {
        return File::exists(self::file_path());
    }

    public static function file_path(): string
    {
        return getenv('HOME') . '/.wpsites.php';
    }

    public static function default_configuration(): string
    {
        return File::get('config/wpsites.php');
    }
}

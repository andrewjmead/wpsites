<?php

namespace App\Domain;

use App\Domain\ConfigTypes\Config;
use Illuminate\Support\Facades\File;

class ConfigFile
{
    public static function get_config(): Config
    {
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

            return $config;
        } catch (\CuyZ\Valinor\Mapper\MappingError $error) {
            // Handle the error…
            echo "invalid mapping";
            echo $error->getMessage();
            dump($error);
            exit(1);
        }
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

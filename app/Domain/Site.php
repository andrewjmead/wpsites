<?php

namespace App\Domain;

use App\Domain\ConfigTypes\Template;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;

use Phar;

use Spatie\Fork\Fork;

use WPConfigTransformer;

class Site
{
    public function __construct(private readonly string $sites_directory, private readonly string $slug)
    {
    }

    public function folder_path(): string
    {
        return Str::rtrim($this->sites_directory, '/').'/'.$this->slug;
    }

    public function execute_alt(string $message, string $command, array $arguments = [], bool $silent = false): void
    {
        if (! $silent) {
            info($message);
        }
        [$success, $output] = $this->execute($command, $arguments);
        if (! $success) {
            error('Error '.strtolower($message));
            File::deleteDirectory($this->folder_path());
            exit(1);
        }
        if (is_string($output)) {
            note($output);
        }
    }

    public function execute(string $command, array $arguments = []): array
    {
        $string_arguments = '';

        $arguments['path'] = $this->folder_path();

        $command = Str::replaceStart('wp ', $this->wp_cli_phar_path().' ', $command);

        foreach ($arguments as $name => $value) {
            if ($value === true) {
                $string_arguments .= " --{$name}";

                continue;
            } elseif ($value === false || $value === '' || $value === null) {
                continue;
            }

            $string_arguments .= " --{$name}=\"{$value}\"";
        }

        $output    = [];
        $exit_code = 1;

        // TODO - Add --verbose option
        exec("{$command} {$string_arguments} &>/dev/null", $output, $exit_code);
        // exec("{$command} {$string_arguments}", $output, $exit_code);

        $output = collect($output)->filter(function ($value) {
            // Remove empty lines
            if (Str::of($value)->trim()->isEmpty()) {
                return false;
            }

            // Remove deprecation notices
            if (Str::contains($value, 'Deprecated')) {
                return false;
            }

            return true;
        })->join("\n");

        if ($output === "") {
            $output = null;
        }

        return [$exit_code === 0, $output];
    }

    public function set_config_transformer(string $key, mixed $value)
    {
        $transformer = new WPConfigTransformer($this->folder_path() . '/wp-config.php');

        if (is_bool($value)) {
            $transformer->update('constant', $key, true ? 'true' : 'false', ['raw' => true]);
        }

        $transformer->update('constant', $key, $value);
    }

    private function wp_cli_phar_path(): string
    {
        if ($phar_path = Phar::running(false)) {
            $phar_directory = pathinfo($phar_path, PATHINFO_DIRNAME);

            return $phar_directory.'/wpsites-wp-cli.phar';
        } else {
            return base_path('builds/wpsites-wp-cli.phar');
        }
    }

    /**
     * @param string $directory
     *
     * @return Collection<Site>
     */
    public static function get_all_slugs(string $directory): Collection
    {
        return self::get_all_sites($directory)->map(fn ($site) => $site->slug);
    }

    /**
     * @param string $directory
     *
     * @return Collection<Site>
     */
    public static function get_all_sites(string $directory): Collection
    {
        $callables = collect(File::directories($directory))->map(function ($site_directory) use ($directory) {
            $site = new Site($directory, basename($site_directory));

            return function () use ($site) {
                [$success, $output] = $site->execute('wp core is-installed');

                return $success ? $site : null;
            };
        });

        $results = Fork::new()->run(
            ...$callables
        );

        return collect($results)->filter();
    }

    // public function template_validation_errors(): ?array
    // {
    //     $validator = $this->make_validator();
    //
    //     if ($validator->passes()) {
    //         return null;
    //     }
    //
    //     return $validator->errors()->all();
    // }

    // private function make_validator()
    // {
    //     return Validator::make($this->attributes->toArray(), [
    //         'name'                   => 'required|string',
    //         'wordpress_version'      => 'nullable|string',
    //         'database_host'          => 'nullable|string',
    //         'database_username'      => 'nullable|string',
    //         'database_password'      => 'nullable|string',
    //         'enable_error_logging'   => 'nullable|boolean',
    //         'enable_automatic_login' => 'nullable|boolean',
    //     ]);
    // }
}

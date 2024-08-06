<?php

namespace App\Domain;

use App\Domain\ConfigTypes\Template;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;

use Phar;

class Site
{
    public function __construct(private readonly string $sites_directory, private readonly string $slug)
    {
    }

    public function folder_path(): string
    {
        return Str::rtrim($this->sites_directory, '/') . '/' . $this->slug;
    }

    public function execute_alt(string $message, string $command, array $arguments = [], bool $silent = false): void
    {
        if (!$silent) {
            info($message);
        }
        list($success, $output) = $this->execute($command, $arguments);
        if (!$success) {
            error("Error " . strtolower($message));
            File::deleteDirectory($this->folder_path());
            exit(1);
        }
        note($output);
    }

    public function execute(string $command, array $arguments = []): array
    {
        $string_arguments = "";

        $arguments['path'] = $this->folder_path();

        $command = Str::replaceStart("wp ", $this->wp_cli_phar_path() . " ", $command);

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

        return [$exit_code === 0, $output];
    }

    private function wp_cli_phar_path(): string
    {
        if ($phar_path = Phar::running(false)) {
            $phar_directory = pathinfo($phar_path, PATHINFO_DIRNAME);

            return $phar_directory . "/wpsites-wp-cli.phar";
        } else {
            return base_path('builds/wpsites-wp-cli.phar');
        }
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

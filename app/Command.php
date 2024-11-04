<?php

namespace App;

use Illuminate\Support\Str;
use Phar;

/**
 * Transform a command and associative array of arguments into a single string command. WP CLI commands
 * that start with "wp " will be modified to use wpsites-wp-cli.phar that ships with the project.
 *
 * The public API is limited to the "from" static method:
 * $string = Command::from('wp some command', [ 'argument' => 'value' ]);
 *
 */
readonly class Command
{
    private function __construct(private string $command, private array $arguments = [])
    {
    }

    private function toString(): string
    {
        return $this->command() . ' ' . $this->arguments();

    }

    private function command(): string
    {
        $command = $this->command;

        if (Str::startsWith($command, 'wp ')) {
            $command = Str::replaceStart('wp ', $this->wp_cli_phar_path() . ' ', $command);
        }

        return $command;
    }

    private function arguments(): string
    {
        $string_arguments = '';

        foreach ($this->arguments as $name => $value) {
            if ($value === true) {
                $string_arguments .= " --{$name}";

                continue;
            } elseif ($value === false || $value === null) {
                continue;
            }

            $string_arguments .= " --{$name}=\"{$value}\"";
        }

        return $string_arguments;
    }

    /**
     * If it's running as a phar, look for wp cli in the phar's directory. If it's not running as phar,
     * look for wp cli in the builds for of the local development setup.
     *
     * @return string
     */
    private function wp_cli_phar_path(): string
    {
        if ($phar_path = Phar::running(false)) {
            $phar_directory = pathinfo($phar_path, PATHINFO_DIRNAME);

            return $phar_directory . '/wpsites-wp-cli.phar';
        } else {
            return base_path('builds/wpsites-wp-cli.phar');
        }
    }

    public static function from(string $command, array $arguments = []): string
    {
        return (new self($command, $arguments))->toString();
    }
}

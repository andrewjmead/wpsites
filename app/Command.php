<?php

namespace App;

use Illuminate\Support\Str;
use Phar;

class Command
{
    public function __construct(private readonly string $command, private readonly array $arguments = []) {}

    public function toString(): string
    {
        return $this->command().' '.$this->arguments();

    }

    private function command(): string
    {
        $command = $this->command;

        if (Str::startsWith($command, 'wp ')) {
            $command = Str::replaceStart('wp ', $this->wp_cli_phar_path().' ', $command);
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

    private function wp_cli_phar_path(): string
    {
        if ($phar_path = Phar::running(false)) {
            $phar_directory = pathinfo($phar_path, PATHINFO_DIRNAME);

            return $phar_directory.'/wpsites-wp-cli.phar';
        } else {
            return base_path('builds/wpsites-wp-cli.phar');
        }
    }

    public static function from(string $command, array $arguments = []): string
    {
        return (new self($command, $arguments))->toString();
    }
}

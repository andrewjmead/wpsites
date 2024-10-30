<?php

namespace App\Domain;

use App\Command;
use App\Zip;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;

use WPConfigTransformer;

class Site
{
    public function __construct(private readonly string $sites_directory, private readonly string $slug)
    {
    }

    public function directory(): string
    {
        return Str::rtrim($this->sites_directory, '/') . '/' . $this->slug;
    }

    public function backup_directory(): string
    {
        return getenv('HOME') . '/.wpsites/backups/' . $this->slug;
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function execute(string $message, string $command, array $arguments = [], bool $print_start_message = true, bool $print_error_message = true, bool $cleanup_on_error = false): void
    {
        if ($print_start_message) {
            info($message);
        }

        File::ensureDirectoryExists($this->directory());

        $command = Command::from($command, $arguments);
        $process = Process::path($this->directory())->run($command);

        if ($process->failed() && $print_error_message) {
            error('Error ' . Str::lower($message));
            error($process->errorOutput());
        }

        if ($process->failed() && $cleanup_on_error) {
            $this->destroy(true);
        }

        if ($process->failed()) {
            exit(1);
        }
    }

    public function destroy(bool $silent = false): void
    {
        // TODO - Probably shouldn't error out
        $this->execute(
            message: "Dropping database for \"{$this->slug}\"",
            command: 'wp db drop',
            arguments: ['yes' => true],
            print_start_message: ! $silent,
            print_error_message: ! $silent,
        );
        File::deleteDirectory($this->directory());
    }

    public function backup(string $backup_name): bool
    {
        $directory = $this->backup_directory() . '/' . time() . '-' . Str::snake($backup_name);

        File::ensureDirectoryExists($directory);

        // TODO I should know if a call to execute succeeded or failed
        $this->execute(
            message: 'Exporting database',
            command: 'wp db export ' . $directory . '/db.sql',
        );

        info('Exporting WordPress files (give it 30 seconds)');
        $success = Zip::archive($this->directory(), $directory . '/files.zip');

        if (!$success) {
            error('Backup failed. Unable to create zip.');
            File::deleteDirectory($directory);

            return false;
        }

        info('Backup saved to ' . $directory);

        return true;
    }

    public function get_backups(): Collection
    {
        return collect(File::directories($this->backup_directory()))
            ->map(function (string $directory) {
                return basename($directory);
            });
    }

    public function restore(string $backup_name): bool
    {
        // TODO
        return true;
    }

    public function set_config(string $key, mixed $value, bool $cleanup_on_error = false): void
    {
        try {
            $transformer = new WPConfigTransformer($this->directory() . '/wp-config.php');

            if (is_bool($value)) {
                $transformer->update('constant', $key, $value === true ? 'true' : 'false', ['raw' => true]);
            } else {
                $transformer->update('constant', $key, $value);
            }
        } catch (\Exception $e) {
            error('Unable to make changes to wp-config.php');
            if ($cleanup_on_error) {
                $this->destroy(true);
            }
            exit(1);
        }
    }

    /**
     * @return Collection<Site>
     */
    public static function get_all_slugs(string $directory): Collection
    {
        return self::get_all_sites($directory)->map(fn ($site) => $site->slug);
    }

    /**
     * @return Collection<Site>
     */
    public static function get_all_sites(string $directory): Collection
    {
        return collect(File::directories($directory))
            ->filter(function ($site_directory) {
                return File::isFile($site_directory . '/wp-config.php');
            })
            ->map(function ($site_directory) {
                return new self(dirname($site_directory), basename($site_directory));
            })
            ->values();
    }
}

<?php

namespace App\Domain;

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
        return Str::finish($this->sites_directory, '/') . $this->slug;
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
            $this->destroy();
        }

        if ($process->failed()) {
            exit(1);
        }
    }

    public function run(string $command, array $arguments = []): bool
    {
        File::ensureDirectoryExists($this->directory());
        $command = Command::from($command, $arguments);
        $process = Process::path($this->directory())->run($command);

        return $process->successful();
    }

    public function destroy(): void
    {
        $has_database = $this->run('wp db check');

        if ($has_database) {
            $this->run('wp db drop', [ 'yes' => true ]);
        }

        File::deleteDirectory($this->directory());
    }

    public function backup(string $backup_name): bool
    {
        $path   = getenv('HOME') . '/.wpsites/backups/' . $backup_name;
        $backup = new Backup($path);

        // Do nothing if the backup folder is already there, regardless of whether it's a valid
        // backup, an empty folder, or anything else.
        if (File::isDirectory($path)) {
            return false;
        }

        File::ensureDirectoryExists($path);

        info('Backing up database');
        $export_process = Process::path($this->directory())->run('wp db export ' . $backup->database_path());

        if ($export_process->failed()) {
            error('Backup failed. Unable to backup database!');
            File::deleteDirectory($path);

            return false;
        }

        info('Backing up files');
        $zip_process = Process::path($this->directory())->run('zip -vr ' . $backup->files_path() . ' * -x "*.DS_Store" --symlinks');

        if ($zip_process->failed()) {
            error('Backup failed. Unable to backup files!');
            File::deleteDirectory($path);

            return false;
        }

        info('Backup successfully created!');
        info('Backup saved to ' . $path);

        return true;
    }

    public function restore(Backup $backup): bool
    {
        if (!$backup->is_valid()) {
            return false;
        }

        File::cleanDirectory($this->directory());

        info('Restoring files');
        $zip_process = Process::run('unzip ' . $backup->files_path() . ' -d ' . $this->directory());

        if ($zip_process->failed()) {
            error('Restore failed. Unable to unzip files.');

            return false;
        }

        $this->set_config('DB_NAME', $this->slug);

        $this->execute(
            message: 'Importing database',
            command: 'wp db import ' . $backup->database_path(),
        );

        $this->execute(
            message: "Setting option \"siteurl\"",
            command: "wp option update siteurl {$this->url()}",
        );

        $this->execute(
            message: "Setting option \"home\"",
            command: "wp option update home {$this->url()}",
        );

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
                $this->destroy();
            }
            exit(1);
        }
    }

    public function url(?string $path = null): string
    {
        $url = "http://{$this->slug}.test";

        if (is_string($path)) {
            $url .= Str::start($path, '/');
        }

        return $url;
    }

    /**
     * @param Collection<string> $directories
     *
     * @return Collection<Site>
     */
    public static function get_sites(Collection $directories): Collection
    {
        return $directories
            ->flatMap(function (string $directory) {
                return collect(File::directories($directory));
            })
            ->filter(function ($site_directory) {
                return File::isFile($site_directory . '/wp-config.php');
            })
            ->map(function ($site_directory) {
                return new self(dirname($site_directory), basename($site_directory));
            })
            ->values();
    }
}

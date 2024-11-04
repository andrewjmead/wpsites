<?php

namespace App\Domain;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use function Symfony\Component\Translation\t;

readonly class Backup
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = Str::rtrim($path, '/');
    }

    public function name(): string
    {
        return basename($this->path);
    }

    public function path(): string
    {
        return $this->path;
    }

    public function database_path(): string
    {
        return $this->path . '/db.sql';
    }

    public function files_path(): string
    {
        return $this->path . '/files.zip';
    }

    public function is_valid(): bool
    {
        if (File::isFile($this->database_path()) && File::isFile($this->files_path())) {
            return true;
        }

        return false;
    }

    public function created_at(): Carbon
    {
        return Carbon::createFromTimestamp(
            File::lastModified($this->path)
        );
    }

    /**
     * @return Collection<self>
     */
    public static function get_backups(): Collection
    {
        $backups_path = getenv('HOME') . '/.wpsites/backups/';

        return collect(File::directories($backups_path))
            ->map(function (string $directory) {
                return new self($directory);
            })
            ->filter(function (self $backup) {
                return $backup->is_valid();
            })
            ->sortByDesc(function (self $backup) {
                return $backup->created_at()->getTimestamp();
            });
    }
}

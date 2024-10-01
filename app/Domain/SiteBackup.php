<?php

namespace App\Domain;

use App\Zip;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;

class SiteBackup
{
    public function __construct(
        private Site $site,
        private string $name
    ) {

    }

    public function run(): bool
    {
        $directory = $this->site->backup_directory() . '/' . time() . '-' . Str::snake($this->name);

        File::ensureDirectoryExists($directory);

        // TODO - You should be able to run your own callback on fail
        $this->site->execute(
            message: 'Exporting database',
            command: 'wp db export ' . $directory . '/db.sql',
        );

        info('Exporting WordPress files');
        $success = Zip::archive($this->site->directory(), $directory . '/files.zip');

        if(!$success) {
            error('Backup failed. Unable to create zip.');
            File::deleteDirectory($directory);
            return false;
        }

        info('Backup saved to ' . $directory);

        return true;
    }
}

<?php

namespace App\Commands;

use Illuminate\Support\Str;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\select;

use LaravelZero\Framework\Commands\Command;

class Restore extends SiteCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore a backup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $backups                = \App\Domain\Backup::get_backups();
        $length_of_longest_name = $backups->map(fn (\App\Domain\Backup $backup) => Str::length($backup->name()))->sortDesc()->first();
        $options                = $backups->mapWithKeys(function (\App\Domain\Backup $backup, int $index) use ($length_of_longest_name) {
            return [
                $backup->path() => Str::padRight($backup->name(), $length_of_longest_name) . ' (' . $backup->created_at()->format('Y-m-d g:i a') . ')',
            ];
        });
        $selected_backup_path = select(
            label: 'Select a backup to use',
            options: $options,
            scroll: 20,
        );
        $selected_backup = $backups->firstWhere(fn (\App\Domain\Backup $backup) => $backup->path() === $selected_backup_path);

        // TODO They should be able to create a new site off of the backup...
        $site = $this->ask_user_for_site('Select a site to restore');

        $success = $site->restore($selected_backup);

        if ($success) {
            info('Backup successfully restored!');
        } else {
            error('Unable to run backup');
        }
    }
}

<?php

namespace App\Commands;

use App\Domain\SiteBackup;

use Carbon\Carbon;
use Illuminate\Support\Str;
use function Laravel\Prompts\error;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

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
        // TODO This should work even if the current site folder is empty or doesn't exist...

        // This is interesting as it requires you to restore to an existing valid WordPress site. This means that you
        // could not restore a corrupt site (files not seen as valid WordPress site) and you could not restore
        // a backup to a new site.

        // You kinda need to pick the backup first, and then decide if you want to wipe an existing site or create
        // a new one...

        $site = $this->ask_user_for_site('Select a site to restore');
        $backups = $site->get_backups();
        $options = $backups->mapWithKeys(function ($backup) {
            $timestamp = Str::before($backup, '-');
            $name = Str::after($backup, '-');
            $date = Carbon::createFromTimestamp($timestamp);

           return [
               $backup => $name . ' (' . $date->format('M j, Y g:i a') . ')'
           ];
        });

        $selected_backup = select(
            label: 'Which backup would you like to use?',
            options: $options,
            scroll: 20,
        );

        $success = $site->restore($selected_backup);

        if (!$success) {
            error('Unable to run backup');
        }
    }
}

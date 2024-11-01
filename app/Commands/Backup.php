<?php

namespace App\Commands;

use function Laravel\Prompts\error;
use function Laravel\Prompts\text;

use LaravelZero\Framework\Commands\Command;

class Backup extends SiteCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup a site';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $site = $this->ask_user_for_site('Select a site to backup');

        $name = text(
            label: 'Pick a name for the backup',
            placeholder: 'backup-name',
            required: true,
            validate: function (string $value) {
                if (!$this->is_valid_kebab_name($value)) {
                    return 'Only lowercase letters, numbers, and hyphens are allowed';
                }

                return null;
            },
        );

        $success = $site->backup($name);

        if (!$success) {
            error('Unable to run backup');
        }
    }
}

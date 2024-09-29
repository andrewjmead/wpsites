<?php

namespace App\Commands;

use function Laravel\Prompts\info;
use function Laravel\Prompts\select;

class Open extends SiteCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'open';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Open a site';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $config = $this->get_config();

        info('Checking which sites are WordPress sites...');
        $slugs = $config->get_all_slugs();

        if ($slugs->count() === 0) {
            info('There are no WordPress sites to open');
            exit(0);
        }

        // TODO - This would let you see the full path while getting back the slug
        //  Could I have $site in the key?
        // $role = select(
        //     label: 'What role should the user have?',
        //     options: [
        //         'member' => 'Member',
        //         'contributor' => 'Contributor',
        //         'owner' => 'Owner',
        //     ],
        //     default: 'owner'
        // );

        $selected_slug = select(
            label: 'Which site would you link to open?',
            options: $slugs,
            scroll: 20,
        );

        exec("open http://{$selected_slug}.test/wp-admin");
    }
}

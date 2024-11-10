<?php

namespace App\Commands;

use App\Domain\Render;
use LaravelZero\Framework\Commands\Command;
use function Termwind\render;

class Playground extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'playground';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A command to test something out';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // TODO
        // Render::list(['key' => 'value' ]);
        Render::green('WPSites');
        Render::text('Loading config files at ...');
        Render::list(
            items: [
                'Site URL' => 'value',
                'Site Directory' =>  'some new value',
            ]
        );

        // Test something out...
        // render(<<<'HTML'
        //     <div>
        //         <div>
        //             <div class="px-1 font-bold text-right bg-green-500 w-18">Site URL:</div>
        //             <em class="ml-1">
        //               Give your CLI apps a unique look
        //             </em>
        //         </div>
        //         <div>
        //             <div class="px-1 font-bold text-right bg-green-500 w-18">Site Directory:</div>
        //             <em class="ml-1">
        //               Give your CLI apps a unique look
        //             </em>
        //         </div>
        //     </div>
        // HTML);
    }
}

<?php

namespace App\Domain\ConfigTypes;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Config
{
    public function __construct(
        /** @var string|list<string> */
        protected $sites_directory,
        /** @var Defaults */
        public readonly Defaults $defaults,
        /** @var list<Template> */
        public readonly array $templates,
    ) {
    }

    /**
     * An array of folders in which sites may live
     *
     * @return Collection<string>
     */
    public function get_site_directories(): Collection
    {
        if (is_string($this->sites_directory)) {
            return collect([
                Str::trim(
                    shell_exec("echo {$this->sites_directory}")
                ),
            ]);
        }

        return collect($this->sites_directory)->map(function (string $sites_directory) {
            return Str::trim(
                shell_exec("echo {$sites_directory}")
            );
        });
    }
}

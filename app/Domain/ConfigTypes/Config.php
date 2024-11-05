<?php

namespace App\Domain\ConfigTypes;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Config
{
    public function __construct(
        /** @var string|non-empty-list<string> */
        protected $sites_directory,
        /** @var Defaults */
        public readonly Defaults $defaults,
        /** @var list<Template> */
        public readonly array $templates,
    ) {
    }

    /**
     * Get a list of folders where WordPress sites may live.
     *
     * @return Collection<string>
     */
    public function get_site_directories(): Collection
    {
        if (is_string($this->sites_directory)) {
            return collect([
                $this->replace_environment_variables($this->sites_directory),
            ]);
        }

        return collect($this->sites_directory)->map(function (string $sites_directory) {
            return $this->replace_environment_variables($sites_directory);
        });
    }

    /**
     * Replace environment variables in a string with their actual values. This will convert a path
     * like $HOME/Herd into a proper absolute path.
     *
     * @param string $string
     *
     * @return string
     */
    private function replace_environment_variables(string $string): string
    {
        return Str::trim(
            shell_exec("echo {$string}")
        );
    }
}

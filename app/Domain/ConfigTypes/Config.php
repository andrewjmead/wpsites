<?php

namespace App\Domain\ConfigTypes;

use App\Domain\Site;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Config
{
    public function __construct(
        /** @var non-empty-string|list<non-empty-string> */
        protected readonly mixed $sites_directory,
        /** @var Defaults */
        public readonly Defaults $defaults,
        /** @var list<Template> */
        public readonly array $templates,
    ) {
    }

    /**
     * @return Collection<string>
     */
    public function get_sites_directories(): Collection
    {
        if (is_string($this->sites_directory)) {
            return collect(
                $this->fill_in_environment_variables($this->sites_directory)
            );
        }

        return collect($this->sites_directory)->map(function (string $directory) {
            return $this->fill_in_environment_variables($directory);
        });
    }

    /**
     * @return Collection<Site>
     */
    public function get_all_sites(): Collection
    {
        return self::get_sites_directories()
            ->map(function (string $directory) {
                return collect(File::directories($directory));
            })
            ->flatten()
            ->filter(function (string $directory) {
                return File::isFile($directory . '/wp-config.php');
            })
            ->map(function ($directory) {
                return new Site(dirname($directory), basename($directory));
            })
            ->values();
    }

    /**
     * @return Collection<Site>
     */
    public function get_all_slugs(): Collection
    {
        return $this->get_all_sites()->map(fn ($site) => $site->slug());
    }

    /**
     * Converts paths like $HOME/Herd into /Users/andrewmead/Herd
     *
     * @param string $path
     *
     * @return string
     */
    private function fill_in_environment_variables(string $path): string
    {
        return Str::trim(
            shell_exec("echo {$path}")
        );
    }
}

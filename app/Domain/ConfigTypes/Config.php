<?php

namespace App\Domain\ConfigTypes;

use Illuminate\Support\Str;

class Config
{
    public function __construct(
        /** @var string */
        protected readonly string $sites_directory,
        /** @var Defaults */
        public readonly Defaults $defaults,
        /** @var list<Template> */
        public readonly array $templates,
    ) {}

    public function get_sites_directory(): string
    {
        return Str::trim(
            shell_exec("echo {$this->sites_directory}")
        );
    }
}

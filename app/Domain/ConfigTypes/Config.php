<?php

namespace App\Domain\ConfigTypes;

class Config {
    public function __construct(
        /** @var string */
        public readonly string $sites_directory,
        /** @var Defaults */
        public readonly Defaults $defaults,
        /** @var list<Template> */
        public readonly array $templates,
    ) {
    }
}

<?php

namespace App\Domain\ConfigTypes;

use DateTimeZone;
use Error;

class Defaults
{
    public function __construct(
        /** @var list<string> */
        public readonly array $plugins = [],
        /** @var ?string */
        public readonly ?string $wordpress_version = null,
        /** @var ?string */
        public readonly ?string $database_host = null,
        /** @var ?string */
        public readonly ?string $database_username = null,
        /** @var ?string */
        public readonly ?string $database_password = null,
        /** @var ?string */
        public readonly ?string $site_title = null,
        /** @var ?string */
        public readonly ?string $admin_username = null,
        /** @var ?string */
        public readonly ?string $admin_password = null,
        /** @var ?string */
        public readonly ?string $admin_email = null,
        /** @var ?bool */
        public readonly ?bool $enable_error_logging = null,
        /** @var ?bool */
        public readonly ?bool $enable_automatic_login = null,
        /** @var ?string */
        public readonly ?string $theme = null,
        /** @var ?bool */
        public readonly ?bool $enable_multisite = null,
        /** @var ?string */
        public readonly ?string $timezone = null,
        /** @var ?string */
        public readonly ?string $wordpress_org_favorites_username = null,
    ) {
        if (is_string($this->timezone) && !in_array($this->timezone, DateTimeZone::listIdentifiers())) {
            throw new Error("Invalid timezone \"{$this->timezone}\" in config file");
        }
    }
}

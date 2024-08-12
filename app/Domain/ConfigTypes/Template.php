<?php

namespace App\Domain\ConfigTypes;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Template
{
    private ?Defaults $defaults = null;

    public function __construct(
        /** @var non-empty-string */
        public readonly string $name,

        /**
         * Defaults
         */

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
    ) {
        $this->defaults = new Defaults;
    }

    public function set_defaults(Defaults $defaults): void
    {
        $this->defaults = $defaults;
    }

    public function get_symlinked_plugins(): Collection
    {
        return $this->get_all_plugins()->filter(function ($plugin) {
            return Str::startsWith($plugin, '/') && File::isDirectory($plugin);
        });
    }

    public function get_repository_plugins(): Collection
    {
        return $this->get_all_plugins()->filter(function ($plugin) {
            return ! Str::startsWith($plugin, '/');
        });
    }

    public function get_all_plugins(): Collection
    {
        $plugins = collect();

        if (is_array($this->plugins)) {
            $plugins = $plugins->merge($this->plugins);
        }

        if (is_array($this->defaults->plugins)) {
            $plugins = $plugins->merge($this->defaults->plugins);
        }

        return $plugins->unique();
    }

    public function get_wordpress_version(): string
    {
        if (is_string($this->wordpress_version)) {
            return $this->wordpress_version;
        }

        if (is_string($this->defaults->wordpress_version)) {
            return $this->defaults->wordpress_version;
        }

        return 'latest';
    }

    public function get_database_host(): string
    {
        if (is_string($this->database_host)) {
            return $this->database_host;
        }

        if (is_string($this->defaults->database_host)) {
            return $this->defaults->database_host;
        }

        return '127.0.0.1';
    }

    public function get_database_username(): string
    {
        if (is_string($this->database_username)) {
            return $this->database_username;
        }

        if (is_string($this->defaults->database_username)) {
            return $this->defaults->database_username;
        }

        return 'root';
    }

    public function get_database_password(): ?string
    {
        if (is_string($this->database_password)) {
            return $this->database_password;
        }

        if (is_string($this->defaults->database_password)) {
            return $this->defaults->database_password;
        }

        return null;
    }

    public function get_site_title(): ?string
    {
        if (is_string($this->site_title)) {
            return $this->site_title;
        }

        if (is_string($this->defaults->site_title)) {
            return $this->defaults->site_title;
        }

        return null;
    }

    public function get_admin_username(): string
    {
        if (is_string($this->admin_username)) {
            return $this->admin_username;
        }

        if (is_string($this->defaults->admin_username)) {
            return $this->defaults->admin_username;
        }

        return 'admin';
    }

    public function get_admin_password(): string
    {
        if (is_string($this->admin_password)) {
            return $this->admin_password;
        }

        if (is_string($this->defaults->admin_password)) {
            return $this->defaults->admin_password;
        }

        return 'password';
    }

    public function get_admin_email(): string
    {
        if (is_string($this->admin_email)) {
            return $this->admin_email;
        }

        if (is_string($this->defaults->admin_email)) {
            return $this->defaults->admin_email;
        }

        return 'admin@example.com';
    }

    public function enable_error_logging(): bool
    {
        if (is_bool($this->enable_error_logging)) {
            return $this->enable_error_logging;
        }

        if (is_bool($this->defaults->enable_error_logging)) {
            return $this->defaults->enable_error_logging;
        }

        return true;
    }

    public function enable_automatic_login(): bool
    {
        if (is_bool($this->enable_error_logging)) {
            return $this->enable_error_logging;
        }

        if (is_bool($this->defaults->enable_error_logging)) {
            return $this->defaults->enable_error_logging;
        }

        return true;
    }

    public function get_theme(): string
    {
        if (is_string($this->theme)) {
            return $this->theme;
        }

        if (is_string($this->defaults->theme)) {
            return $this->defaults->theme;
        }

        return 'twentytwentyfour';
    }

    public function enable_multisite(): bool
    {
        if (is_bool($this->enable_multisite)) {
            return $this->enable_multisite;
        }

        if (is_bool($this->defaults->enable_multisite)) {
            return $this->defaults->enable_multisite;
        }

        return false;
    }
}

<?php

namespace App\Commands;

use App\Domain\ConfigFile;
use App\Domain\ConfigTypes\Template;
use App\Domain\Site;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class Create extends SiteCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new site from your templates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $config = ConfigFile::parse();

        $options = collect($config->templates)->map(function (Template $template) {
            return $template->name;
        })->all();

        $selected_template_name = select(
            label: 'Select a template to use',
            options: $options,
            scroll: 20,
        );

        /** @var Template $template */
        $template = collect($config->templates)->first(function (Template $template) use ($selected_template_name) {
            return $template->name === $selected_template_name;
        });

        $slug = text(
            label: 'Pick a slug for the site',
            placeholder: 'my-site',
            default: $template->default_slug(),
            required: true,
            validate: function (string $value) {
                if (!$this->is_valid_kebab_name($value)) {
                    return 'Only lowercase letters, numbers, and hyphens are allowed';
                }

                return null;
            },
            hint: 'This will be used for the sites folder name, the database name, etc'
        );

        $sites_directory = $config->get_site_directories()->first();

        // If there are multiple site directories, have the user pick which they want to use.
        if ($config->get_site_directories()->count() > 1) {
            $sites_directory = select(
                label: 'Select a site directory to use',
                options: $config->get_site_directories(),
                scroll: 20,
            );
        }

        $site = new Site($sites_directory, $slug);

        // TODO Site slugs need to be unique across all site directories as the database name might conflict
        //  either that or just detect conflicts as the site is created...
        if (File::isDirectory($site->directory())) {
            $should_override_site = confirm(
                label: 'Site already exists! Override it?',
                default: false,
            );

            if ($should_override_site) {
                info('Destroying existing site');
                $site->destroy();
            } else {
                exit(1);
            }
        }

        $site->execute(
            message: 'Downloading core files',
            command: 'wp core download',
            arguments: [
                'skip-content' => true,
                'version'      => $template->get_wordpress_version(),
            ],
            cleanup_on_error: true,
        );

        $site->execute(
            message: 'Creating site',
            command: 'wp config create',
            arguments: [
                'dbhost' => $template->get_database_host(),
                'dbname' => $slug,
                'dbuser' => $template->get_database_username(),
                'dbpass' => $template->get_database_password(),
            ],
            cleanup_on_error: true,
        );

        $site->execute(
            message: 'Creating database',
            command: 'wp db create',
            cleanup_on_error: true,
        );

        $site->execute(
            message: 'Running installation',
            command: 'wp core ' . ($template->enable_multisite() ? 'multisite-install' : 'install'),
            arguments: [
                'url'            => $site->url(),
                'title'          => $template->get_site_title() ?? $selected_template_name,
                'admin_user'     => $template->get_admin_username(),
                'admin_password' => $template->get_admin_password(),
                'admin_email'    => $template->get_admin_email(),
            ],
            cleanup_on_error: true,
        );

        if ($template->enable_multisite()) {
            $site->execute(
                message: 'Creating a second site for the multisite',
                command: 'wp site create',
                arguments: [
                    'slug'  => 'second-site',
                    'title' => 'A second site',
                    'email' => $template->get_admin_email(),
                ],
                cleanup_on_error: true,
            );
        }

        if ($template->enable_automatic_login()) {
            info('Enabling automatic login');

            $site->set_config('WP_ENVIRONMENT_TYPE', 'local');
            $site->set_config('AUTOMATIC_LOGIN_USER_LOGIN', $template->get_admin_username());
            $site->set_config('AUTOMATIC_LOGIN_USER_PASSWORD', $template->get_admin_password());

            $site->execute(
                message: 'Installing automatic-login',
                command: 'wp plugin install automatic-login',
                arguments: [
                    'activate' => true,
                ],
                cleanup_on_error: true,
            );
        }

        if ($template->enable_error_logging()) {
            info('Enabling error logging');

            $site->set_config('WP_DEBUG', true);
            $site->set_config('WP_DEBUG_LOG', true);
            $site->set_config('WP_DEBUG_DISPLAY', false);
        }

        if (is_string($template->get_timezone())) {
            $site->execute(
                message: "Setting timezone to \"{$template->get_timezone()}\"",
                command: "wp option update timezone_string {$template->get_timezone()}",
                cleanup_on_error: true,
            );
        }

        if (is_string($template->get_wordpress_org_favorites_username())) {
            $site->execute(
                message: "Setting favorites username to \"{$template->get_wordpress_org_favorites_username()}\"",
                command: "wp user meta add 1 wporg_favorites {$template->get_wordpress_org_favorites_username()}",
                cleanup_on_error: true,
            );
        }

        $theme = $template->get_theme();

        if (Str::startsWith($theme, '/') && File::isDirectory($theme)) {
            info("Linking theme at \"{$theme}\"");
            $symlink_name = basename($theme);
            symlink($theme, $site->directory() . '/wp-content/themes/' . $symlink_name);
            $site->execute(
                message: "Linking \"{$theme}\"",
                command: "wp theme activate {$symlink_name}",
                print_start_message: false,
                cleanup_on_error: true,
            );
        } else {
            $theme_slug = $theme;
            $version    = null;

            if (Str::contains($theme, '@')) {
                $theme_slug = Str::before($theme, '@');
                $version    = Str::after($theme, '@');
            }

            $site->execute(
                message: "Installing theme \"{$theme}\"",
                command: "wp theme install {$theme_slug} --activate",
                arguments: [
                    'version' => $version,
                ],
                cleanup_on_error: true,
            );
        }

        $template->get_symlinked_plugins()->each(function ($plugin) use ($site) {
            info("Linking plugin at \"{$plugin}\"");
            $symlink_name = basename($plugin);
            symlink($plugin, $site->directory() . '/wp-content/plugins/' . $symlink_name);

            $site->execute(
                message: "Linking \"{$plugin}\"",
                command: "wp plugin activate {$symlink_name}",
                print_start_message: false,
                cleanup_on_error: true,
            );
        });

        $template->get_repository_plugins()->each(function ($plugin) use ($site) {
            $plugin_slug = $plugin;
            $version     = null;

            if (Str::contains($plugin, '@')) {
                $plugin_slug = Str::before($plugin, '@');
                $version     = Str::after($plugin, '@');
            }

            $site->execute(
                message: "Installing \"{$plugin}\"",
                command: "wp plugin install {$plugin_slug}",
                arguments: [
                    'activate' => true,
                    'version'  => $version,
                ],
                cleanup_on_error: true,
            );
        });

        $template->get_constants()->whenNotEmpty(function () {
            info('Setting constants');
        })->each(function (string $value, string $key) use ($site) {
            $site->set_config($key, $value);
        });

        $template->get_options()->whenNotEmpty(function () {
            info('Setting options');
        })->each(function (mixed $value, string $key) use ($site) {
            $site->set_option($key, $value);
        });

        info('Site created!');
        info('Directory: ' . $site->directory());
        info('URL: ' . $site->url());

        if ($config->should_open_new_site()) {
            info('Opening site...');
            exec("open {$site->url('/wp-admin')}");
        }
    }
}

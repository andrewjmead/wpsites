<?php

namespace App\Commands;

use App\Domain\ConfigTypes\Template;
use App\Domain\Site;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

use WPConfigTransformer;

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
        $config = $this->get_config();

        $options = collect($config->templates)->map(function (Template $template) {
            return $template->name;
        })->toArray();

        $selected_template_name = select(
            label: 'Which template would you like to use?',
            options: $options,
        );

        /** @var Template $template */
        $template = collect($config->templates)->first(function (Template $template) use ($selected_template_name) {
            return $template->name === $selected_template_name;
        });

        $slug = text(
            label: 'What slug would you like to use?',
            placeholder: 'my-site',
            default: $template->default_slug(),
            required: true,
            validate: function (string $value) {
                if (Str::slug($value) !== $value) {
                    return 'Site name must be a slug!';
                }

                return null;
            },
            hint: 'This will be used for the sites folder name, the database name, etc...'
        );

        $site = new Site($config->get_sites_directory(), $slug);

        // if ($site->template_validation_errors()) {
        //     error("The template for {$site->name()} is invalid!");
        //     collect($site->template_validation_errors())->each(function ($error) {
        //         error("* {$error}");
        //     });
        //     exit(1);
        // }

        if (file_exists($site->folder_path())) {
            $should_override_site = confirm(
                label: 'Site already exists! Override it?',
                default: false,
            );

            if ($should_override_site) {
                note('Deleting existing site...');
                $site->execute_alt('Dropping database...', 'wp db drop', [
                    'yes' => true,
                ], true);
                File::deleteDirectory($site->folder_path());
                info('Existing site deleted!');
            } else {
                exit(1);
            }
        }

        $site->execute_alt('Downloading core files...', 'wp core download', [
            'skip-content' => true,
            'version'      => $template->get_wordpress_version(),
        ]);

        $site->execute_alt('Creating site...', 'wp config create', [
            'dbhost' => $template->get_database_host(),
            'dbname' => $slug,
            'dbuser' => $template->get_database_username(),
            'dbpass' => $template->get_database_password(),
        ]);

        $site->execute_alt('Creating database...', 'wp db create');

        if ($template->enable_multisite()) {
            $site->execute_alt('Running installation...', 'wp core multisite-install', [
                'url'            => "http://{$slug}.test",
                'title'          => $template->get_site_title() ?? $selected_template_name,
                'admin_user'     => $template->get_admin_username(),
                'admin_password' => $template->get_admin_password(),
                'admin_email'    => $template->get_admin_email(),
            ]);
            $site->execute_alt('Creating a second site...', 'wp site create', [
                'slug'  => 'second-site',
                'title' => 'A second site',
                'email' => $template->get_admin_email(),
            ]);
        } else {
            $site->execute_alt('Running installation...', 'wp core install', [
                'url'            => "http://{$slug}.test",
                'title'          => $template->get_site_title() ?? $selected_template_name,
                'admin_user'     => $template->get_admin_username(),
                'admin_password' => $template->get_admin_password(),
                'admin_email'    => $template->get_admin_email(),
            ]);
        }

        if ($template->enable_error_logging()) {
            info('Enabling error logging...');
            try {
                $site->set_config_transformer('WP_DEBUG', 'true');
                $site->set_config_transformer('WP_DEBUG_LOG', 'true');
                $site->set_config_transformer('WP_DEBUG_DISPLAY', 'false');
            } catch (\Throwable $e) {
                // TODO - Roll back
            }
            // // There are issues with this plugin. I need to fine one that doesn't try to manipulate the values, but just shows the file...
            // // $site->execute_alt("Enabling error log...", "wp plugin install wp-debugging", [
            // //     'activate' => true,
            // // ], true);
        }

        if ($template->enable_automatic_login()) {
            info('Enabling automatic login...');
            try {
                $site->set_config_transformer('WP_ENVIRONMENT_TYPE', 'local');
                $site->set_config_transformer('AUTOMATIC_LOGIN_USER_LOGIN', $template->get_admin_username());
                $site->set_config_transformer('AUTOMATIC_LOGIN_USER_PASSWORD', $template->get_admin_password());
            } catch (\Throwable $e) {
                // TODO - Roll back
            }
            $site->execute_alt('Enabling automatic login...', 'wp plugin install automatic-login', [
                'activate' => true,
            ], true);
        }

        $site->execute_alt('Installing default theme...', "wp theme install {$template->get_theme()}", [
            'activate' => true,
        ]);

        $template->get_symlinked_plugins()->each(function ($plugin) use ($site) {
            info("Linking \"{$plugin}\"...");
            $symlink_name = basename($plugin);
            symlink($plugin, $site->folder_path().'/wp-content/plugins/'.$symlink_name);

            $site->execute_alt("Linking \"{$plugin}\"...", "wp plugin activate {$symlink_name}", [], true);
        });

        $template->get_repository_plugins()->each(function ($plugin) use ($site) {
            $site->execute_alt("Installing \"{$plugin}\"...", "wp plugin install {$plugin}", [
                'activate' => true,
            ]);
        });

        info('Opening site...');

        exec("open http://{$slug}.test/wp-admin");
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $autoloadPath = $GLOBALS['_composer_autoload_path'] ?? null;

        if (! is_string($autoloadPath)) {
            return;
        }

        $installedPackagesPath = dirname($autoloadPath) . '/composer/installed.php';

        if (! file_exists($installedPackagesPath)) {
            return;
        }

        $installedPackages = require $installedPackagesPath;
        $installedVersion  = $installedPackages['versions']['andrewmead/wpsites']['pretty_version'] ?? null;

        if (is_string($installedVersion)) {
            $this->app['config']->set('app.version', $installedVersion);
        }
    }
}

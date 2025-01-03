# WPSites

WPSites is the fastest way to create a localhost WordPress website.

All you need to do is run `wpsites create`, select a template, name your site, and—boom! It's live!

Check it out:


https://github.com/user-attachments/assets/77ca8ba5-2e6e-45be-9d75-9e16f6056120



# Contents 

1. [Why?](#why)
1. [Getting started](#getting-started)
1. [Commands](#commands)
    1. [Config](#config)
    1. [Create](#create)
    1. [Destroy](#destroy)
    1. [Open](#open)
    1. [Backup](#backup)
    1. [Restore](#restore)
1. [Configuring WPSites](#configuring-wpsites)
1. [Template options](#template-options)
1. [Limitations](#limitations)

# Why?

I've been building [Independent Analytics](https://wordpress.org/plugins/independent-analytics/) for the last 2 years, and during that time I've created hundreds (thousands?) of temporary WordPress sites.

I would make a new site for development, to test customer issues, to track down regression in specific versions, and for countless other reasons.

**It was the same process over and over again. I'd create a new site only to configure it exactly the same way I'd done countless times before. Enable debug mode. Symlink the local build. Install the plugins we integrate with. The list goes on and on.**

No more.

Now I run `wpsites create`, select the template I want to use, and 10 seconds later I'm looking at the admin panel for my new WordPress site.

It's sublime.

This project has been a joy to work on. I hope you get some value out of it. If you run into any problems, please open an issue. I'd like to try to get WPSites working for as many people on as many different setups as possible.

# Getting started

### Installing

You'll want to install WPSites as a global composer package. This will give you access to the `wpsites` command from anywhere on your machine.

```
composer global require andrewmead/wpsites
```

The installation process will place the PHAR executable in `~/.composer/vendor/bin/`. Make sure this directory is part of your PATH, otherwise commands such as `wpsites create` will not work.

### Generating a config file

Before you can create your first WordPress site, you'll need to generate a config file. You can do this by running `wpsites config`. This command will copy the default config file to `~/.wpsites.php`.

```
$ wpsites config

 Copying default config to `/Users/andrewmead/.wpsites.php`

 Config file successfully created!
```

Once your config file is created, open it in your text editor as there are a couple of values you'll need to change before you can create your first site.

### Configuring your site's directory

You need to tell WPSites where on your file system you want new sites to be created. This can be done by changing the value for `sites_directory` near the top of `~/.wpsites.php`.

```php
// ~/.wpsites.php

return [
    // ...
    'sites_directory' => '$HOME/Herd',
    // ...
];
```

The default value of `$HOME/Herd` will work if you are using Laravel Herd. If you're using MAMP PRO, you'll need to change the path to `$HOME/Sites`.

You can store your sites anywhere, but make sure the site's directory is being served up by your localhost server.

### Configuring your database connection

Aside from the site's directory, the only other thing you need to configure is your database connection.

There are three options you can use for this. Inside of `defaults`, you'll want to use `database_host`, `database_username`, and `database_password`.

By default, WPSites will try to connect to `127.0.0.1:3306` as the user `root` without a password. Change these values to match up with your localhost database server.

```php
// ~/.wpsites.php

return [
    // ...
    'defaults' => [
        // ...
        'database_host'     => '127.0.0.1:3306',
        'database_username' => 'root',
        'database_password' => null,
        // ...
    ]
];
```

You can test your database connection in the next step by trying to create a new site.

### Creating your first site

You're now ready to create your first site!

Create your first site by running `wpsites create`. You'll be prompted to pick a template. Select "Basic WordPress" for now. We'll talk more about templates a bit later. Next, pick a slug for your site.

In a few seconds, you should be looking at your brand-new WordPress site!

```
$ wpsites create

 ┌ Which template would you like to use? ───────────────────────┐
 │ › ● Basic WordPress                                          │
 │   ○ Basic Multisite WordPress                                │
 │   ○ Symlink plugin example                                   │
 │   ○ Bug recreation example                                   │
 └──────────────────────────────────────────────────────────────┘

 ┌ What slug would you like to use? ────────────────────────────┐
 │ any-slug-you-like                                            │
 └──────────────────────────────────────────────────────────────┘

 Downloading core files...

 Creating site...

 Creating database...

 Running installation...

 Enabling error log...

 Enabling automatic login...

 Installing default theme...

 Opening site...
```

Amazing!

Notice that you didn't need to log in to the admin panel. This is thanks to the `automatic-login` plugin. New sites have automatic login enabled. They also have the debug log enabled. There are settings in [template options](#template-options) to disable both of these, but I find it's perfect for local development.

Creating your first site is just the beginning. Read on to learn how you can make your own templates to create sites specific to your needs!

# Commands

1. [Config](#config)
1. [Create](#create)
1. [Destroy](#destroy)
1. [Open](#open)
1. [Backup](#backup)
1. [Restore](#restore)

### Config

Run `wpsites config` to create the default config file. You can rerun `wpsites config` to reset the config file back to its default state.

```
$ wpsites config

 Loading config file at "/Users/andrewmead/.wpsites.php"

 Copying default config to `/Users/andrewmead/.wpsites.php`

 Config file successfully created!
```

### Create

Run `wpsites create` to create a new WordPress site based on a template in your config file.

```
$ wpsites create

 Loading config file at "/Users/andrewmead/.wpsites.php"
 
 ┌ Which template would you like to use? ───────────────────────┐
 │ › ● Basic WordPress                                          │
 │   ○ Basic Multisite WordPress                                │
 │   ○ Symlink plugin example                                   │
 │   ○ Bug recreation example                                   │
 └──────────────────────────────────────────────────────────────┘

 ┌ What slug would you like to use? ────────────────────────────┐
 │ any-slug-you-like                                            │
 └──────────────────────────────────────────────────────────────┘

 Downloading core files...

 Creating site...

 Creating database...

 Running installation...

 Enabling error log...

 Enabling automatic login...

 Installing default theme...

 Opening site...
```

### Destroy

Run `wpsites destroy` to destroy one or more sites.

```
$ wpsites destroy

 Loading config file at "/Users/andrewmead/.wpsites.php"

 Checking which sites are WordPress sites...

 ┌ Which sites would you like to destroy? ──────────────────────┐
 │ wp-test                                                      │
 │ iawp                                                         │
 └──────────────────────────────────────────────────────────────┘

 ┌ Are you sure you want to destroy the sites listed above? ────┐
 │ Yes                                                          │
 └──────────────────────────────────────────────────────────────┘

 Deleting site "wp-test"

 Deleting site "iawp"
```

### Open

Run `wpsites open` to open an existing site in your browser.

```
➜  ~ wpsites open

 Loading config file at "/Users/andrewmead/.wpsites.php"

 Checking which sites are WordPress sites...

 ┌ Select a site to open ───────────────────────────────────────┐
 │ › ● iawp                                                     │
 │   ○ latest-production-install                                │
 │   ○ woocommerce                                              │
 └──────────────────────────────────────────────────────────────┘
```

### Backup

Run `wpsites backup` to create a backup of an existing site.

Backups are created in `~/.wpsites/backups/`. Each backup contains `db.sql` and `files.zip`.

These backups are not created in a proprietary format. The database export is created using WP CLI's [`wp db export`](https://developer.wordpress.org/cli/commands/db/export/). The file export is just a zip of the WordPress files. That means you can take them with you if you decide to stop using WPSites.

```
➜  ~ wpsites backup

 Loading config file at "/Users/andrewmead/.wpsites.php"

 Checking which sites are WordPress sites...

 ┌ Select a site to backup ─────────────────────────────────────┐
 │ iawp                                                         │
 └──────────────────────────────────────────────────────────────┘

 ┌ Pick a name for the backup ──────────────────────────────────┐
 │ before-migration                                             │
 └──────────────────────────────────────────────────────────────┘

 Backing up database

 Backing up files

 Backup successfully created!

 Backup saved to /Users/andrewmead/.wpsites/backups/before-migration
```

### Restore

Run `wpsites restore` to restore a backup.

```
➜  ~ wpsites restore

 Loading config file at "/Users/andrewmead/.wpsites.php"

 ┌ Select a backup to use ──────────────────────────────────────┐
 │ before-migration (2024-11-04 2:51 pm)                        │
 └──────────────────────────────────────────────────────────────┘

 Checking which sites are WordPress sites...

 ┌ Select a site to restore ────────────────────────────────────┐
 │ iawp                                                         │
 └──────────────────────────────────────────────────────────────┘

 Restoring files

 Importing database

 Backup successfully restored!
```

# Configuring WPSites

In this section, you'll learn how to customize WPSites to fit your needs. This includes defining a reasonable set of defaults, as well as defining your own templates so you can quickly spin up a preconfigured site.

### Exploring the default config file

To get started, let's take a look at the default config file that was created when you initially ran `wpsites config`.

Here's the entirety of the default config file.

```php
<?php

// Explore every option that you can set:
// https://github.com/andrewjmead/wpsites#template-options

return [
    'sites_directory' => '$HOME/Herd',
    'defaults'        => [
        'database_host'     => '127.0.0.1:3306',
        'database_username' => 'root',
        'database_password' => null,
    ],
    'templates' => [
        [
            'name' => 'Basic WordPress',
        ],
        [
            'name'             => 'Basic Multisite WordPress',
            'enable_multisite' => true,
        ],
        [
            'name'              => 'Example with more options',
            'wordpress_version' => '5.9.10',
            'plugins'           => [
                'independent-analytics',
                '/plugin/path/to/symlink',
            ],
            'theme' => 'twentytwentythree'
        ],
    ],
];
```

The config file is nothing more than a PHP file that returns an associative array. This associative array is where you can customize WPSites and define your own templates.

There are three top-level properties. 

**First up is `sites_directory`.**

The value for this should be the path to an existing directory on your computer.

This directory is where WPSites will create new WordPress sites, so make sure the directory is being served up by whatever PHP server you're running.

If you're using Laravel Herd, the default value of `'$HOME/Herd'` should work great. If you're using MAMP PRO, you'll want to use `'$HOME/Sites'`.

You can also set `sites_directory` to an array of directories. This will let you create and manage sites from multiple directories, such as having a "work" directory for work sites and a "personal" directory for person sites.

**Next up is `defaults`.**

As you can see in the config file above, the value for `defaults` is an associative array.

On this array, you can define a set of default options that you want to apply to all sites you create. Keep in mind that any option that can be set on `defaults` can also be set on a template. The template value will override whatever default value was set.

The default config file defines the three options that are used to configure the database connection. These are `database_host`, `database_username`, and `database_password`. You may need to tweak these options to match up with your local MySQL database server.

**Last up is `templates`**

The value for `templates` is an array of associated arrays.

Each item in the array represents a template that you can use when creating a new site. The only attribute you have to define is `name`. Set `name` equal to a string that describes the site.  

The default config file above has three templates. The first template is a simple one that only sets a name. The second one is similar to the first, though it sets `enable_multisite` to `true` to create a multisite. The final template uses a few more options to customize the site. It uses `wordpress_version` to change the version of WordPress that's used. It uses `theme` to customize the theme. Finally, it uses `plugins` to define a list of plugins that should be installed.

Take a look at [template options](#template-options) below to see what's possible.

**Another option is `should_open_new_site`**

When a new site is created, WPSites tries to open the site in the browser.

You can turn off this behavior by setting `should_open_new_site` to `false`.

### Making your own templates

You can make a new template by adding an associative array to the end of `templates`.

The only property you have to define is `name`. After setting a name, you'll see your new template listed as an option the next time you run `wpsites create`.

Below is my actual config file. Take a moment to check it out then I'll point out some notable things below.

```php
<?php

return [
    'sites_directory' => '$HOME/Herd',
    'defaults'        => [
        'plugins' => [
            'code-snippets'
        ]
    ],
    'templates' => [
        [
            'name' => 'Basic WordPress',
        ],
        [
            'name'             => 'Basic multisite WordPress',
            'enable_multisite' => true
        ],
        [
            'name'    => 'IAWP Dev',
            'plugins' => [
                '/Users/andrewmead/Projects/independent-analytics/independent-analytics',
                '/Users/andrewmead/Projects/iawp-developer-niceties',
                'woocommerce',
                'woo-order-test',
                'surecart',
            ],
        ],
        [
            'name'             => 'IAWP Dev Multisite',
            'enable_multisite' => true,
            'plugins'          => [
                '/Users/andrewmead/Projects/independent-analytics/independent-analytics',
                '/Users/andrewmead/Projects/iawp-developer-niceties',
                'woocommerce',
                'woo-order-test',
                'surecart',
            ],
        ],
        [
            'name'    => 'IAWP Latest Stable Release',
            'plugins' => [
                'independent-analytics',
            ],
        ],
    ],
];
```

First up, notice that there are no database options defined in `defaults`. Actually, there are no database options defined anywhere.

For my machine, which WPSites was built for, the default values for `database_host`, `database_username`, and `database_password` don't need to be changed. Setting `database_host` equal to `127.0.0.1:3306` is unnecessary as that's already the default value for the option. Every option has a default value, and if you're happy with the default value then there's no need to configure it.

You can find the default value for all the options in [template options](#template-options) below.

The one option that I have defined in `defaults` is `plugins`. This lets me define a set of plugins that I want to use on all new sites. If a template also defines `plugins`, the default plugins and the template plugins will be merged and all plugins will be installed.

Below `defaults` is `templates`. I have 5 templates defined.

The first two are the basic site and multisite templates that come with the default config file. Not super interesting.

The third template is "IAWP Dev". This is the template I use for my main development site as I'm building out Independent Analytics. It symlinks a couple of local plugins and also installs some third-party plugins that we integrate with.

The fourth template is the same as the third, though it's a multisite.

The fifth and final template is a site that installs the last released version of Independent Analytics. This installs the plugin from the WordPress plugin repository, which is convenient when I need to recreate a customer issue with only the features that have already been released.

# Template options

Below is every option that WPSites supports. These options can be set inside of `defaults` or inside of a specific template.

Options defined in a template will override options defined in `defaults`.

A few options work a bit differently. For `plugins`, `constants`, and `options`, the template value is mereged with the default value. For `plugins`, this means you can set template specific plugins while also having default plugins that all sites use. For `constants` and `options`, this means that you can set default values and override them in a specific template.

1. [Plugins](#plugins)
1. [Theme](#theme)
1. [WordPress Version](#wordpress-version)
1. [Enable multisite](#enable-multisite)
1. [Enable error logging](#enable-error-logging)
1. [Enable automatic login](#enable-automatic-login)
1. [Database host](#database-host)
1. [Database username](#database-username)
1. [Database password](#database-password)
1. [Database name](#database-name)
1. [Admin username](#admin-username)
1. [Admin email](#admin-email)
1. [Admin password](#admin-password)
1. [Timezone](#timezone)
1. [Wordpress.org favorites username](#wordpressorg-favorites-username)
1. [Constants](#constants)
1. [Options](#options)

### Plugins

Option: `plugins`

Default: `[]`

An array of plugins to install.

Use a slug like `woocommerce` to install a plugin from the WordPress repository. Install a specific version by appending `@` and the version like `woocommerce@9.3.1`.

Use an absolute path like `/plugin/to/symlink` to symlink a local plugin on your machine.

```php
[
    'plugins' => [
        '/Users/andrew/projects/independent-analytics',
        '/Users/andrew/projects/iawp-developer-niceties',
        'woocommerce@9.3.1',
        `woo-order-test`,
    ]
]
```

### Theme

Option: `theme`

Default: `'twentytwentyfour'`

The theme to install.

Use a slug like `twentytwentyfour` to install a theme from the WordPress repository. Install a specific version by appending `@` and the version like `twentytwentyfour@1.2`.

Use an absolute path like `/theme/to/symlink` to symlink a local theme on your machine.

### WordPress version

Option: `wordpress_version`

Default: `'latest'`

The version of WordPress your site will use. Valid values include 'latest', 'nightly', or a WordPress version such as '6.0.0'.

### Enable multisite

Option: `enable_multisite`

Default: `false`

Enable to create a multisite. This will also create a second site as part of the multisite network.

### Enable error logging

Option: `enable_error_logging`

Default: `true`

Enable to setup error logging. This sets WP_DEBUG to true, WP_DEBUG_LOG to true, and WP_DEBUG_DISPLAY to false.

### Enable automatic login

Option: `enable_automatic_login`

Default: `true`

Enable automatically logging in to the admin panel. This is done by installing and configuring the automatic-login plugin.

### Database host

Option: `database_host`

Default: `'127.0.0.1:3306'`

The host (and port!) for your site's database connection.

### Database username

Option: `database_username`

Default: `'root'`

The username for your site's database connection.

### Database password

Option: `database_password`

Default: `null`

The password to for your site's database connection. A value of null should be used if there is no password.

### Database name

There is no option to define the database name. Instead, the slug you provide running `wpsites create` is used as the database name.

### Admin username

Option: `admin_username`

Default: `'admin'`

The username for the admin user.

### Admin email

Option: `admin_email`

Default: `'admin@example.com'`

The email for the admin user.

### Admin password

Option: `admin_password`

Default: `'password'`

The password for the admin user.

### Timezone

Option: `timezone`

Default: `'UTC'`

The timezone for the site. Examples include `America/New_York`, `America/Denver`, and `Europe/Berlin`.

Take a look at the return value for `DateTimeZone::listIdentifiers()` to see supported timezones.

### Wordpress.org favorites username

Option: `wordpress_org_favorites_username`

Default: `null`

WordPress.org lets logged-in users favorite plugins and themes. You can provide your WordPress.org username here to have your favorites show up under the "Favorites" tab when adding a new plugin or theme.

### Constants

Option: `constants`

Default: `[]`

An array of constants to set in `wp-config.php`. The value should be an associative array of key-value pairs. The key is the constant name. The value is the constant's value.

```php
[
    'constants' => [
        'MY_CONSTANT'         => 'The value',
        'MY_CONSTANT_NUMBER'  => 42,
        'MY_CONSTANT_BOOLEAN' => false,
    ]
]
```

### Options

Option: `options`

Default: `[]`

An array of options to set in the options database table. The value should be an associative array of key-value pairs. The key is the option name. The value is the option's value.

```php
[
    'options' => [
        'prefix_option'         => 'The value',
        'prefix_option_number'  => 42,
        'prefix_option_boolean' => false,
    ]
]
```

# Limitations

I only use macOS, so I haven't verified that WPSites does (or doesn't!) work on Windows or Linux. I hope to get it running in those environments at some point. If you run into problems, please open an issue so I can support those platforms.

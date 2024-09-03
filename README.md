# WPSites

WPSites is the fastest way to create a localhost WordPress website.

Just run `wpsites create`, select a site template, and give your site a name. Boom. Done.

https://github.com/user-attachments/assets/b04fc4d8-7603-4729-b399-db08e4b1da6a

# Contents 

[Why?](#why)

[Getting started](#getting-started)

[Configuring WPSites](#configuring-wpsites)

[Template options](#template-options)

# Why?

I've been building [Independent Analytics](https://wordpress.org/plugins/independent-analytics/) for the last 2 years, and during that time I've created hundreds (thousands?) of temporary WordPress sites.

I'd create new sites for development. I'd create new sites to test customer issues. I'd create new sites to track down regression.

**It was the same process over and over again. I'd create a new site only to configure it exactly the same way as I'd done countless times before. Enable debug mode. Symlink the local build. Install the plugins we integrate with. The list goes on and on.**

No more.

Now I run `wpsites create`, select which of my templates I want to use, and wait 10 seconds as the new site is created.

It's sublime.

This has been the most genuily usefull project I've created. I hope you get some value out of it. If you do, [say hi](https://twitter.com/andrew_j_mead). I'd love to hear from you.

# Getting started

Please open an issue if you run into any issues with the software or with this guide. I can't test the project on every setup, so I rely on your bug reports to make sure WPSites works on a wide range of machines.

### Installing

You're gonna want to install WPSites as a global composer package. This will give you access to the `wpsites` command from anywhere on your machine.

```
composer global require andrewmead/wpsites
```

The installation process will place the PHAR executable in `~/.composer/vendor/bin/`. Make sure this directory is part of your PATH, otherwise commands such as `wpsites create` will not work.

### Generating a config file

Before you can create your first WordPress site, you'll need to generate a config file. You can do this by running `wpsites config`. This command will copy the default config file to `~/.wpsites.php`.

```
$ wpsites conifg

 Copying default config to `/Users/andrewmead/.wpsites.php`

 Config file successfully created!
```

From here, open the config file in your text editor. There are a couple things you'll need to tweak before you can create your first site.

### Configure your sites directory

You need to tell WPSites where on your file system you want it to create new sites. This can be done by changing the value for `sites_directory` near the top of `~/.wpsites.php`.

```php
// ~/.wpsites.php

return [
    // ...
    'sites_directory' => '$HOME/Herd',
    // ...
];
```

The default value of `$HOME/Herd` will work if you are using Laravel Herd. If you're using MAMP PRO, you'll need to change the path to `$HOME/Sites`.

You can store your sites anywhere, but make sure the site directory is being served up by your localhost server.

### Configuring your database connection

Aside from the sites directory, the only other thing you need to configure is your database connection.

There are three options you can use for this. Inside of `defaults`, you'll want to use `database_host`, `database_username`, and `database_password`.

By default, WPSites will try to connect to `127.0.0.1:3306` as the user `root`. Customize these three values to connect to whatever local MySQL database you're running.

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

Create your first site by running `wpsites create`. You'll be prompted to pick a template, just select "Basic WordPress" for now. We'll talk more about templates a bit later. From there, pick a slug for your site and in a few seconds you should be looking at your brand new WordPress site!

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

Amazing! Now creating your first site is just the beginning. Read on to learn how you can make your own templates to create sites specific to your needs!

# Configuring WPSites

In this section, you'll learn how to customize WPSites to fit your needs. This includes defining a reasonable set of defaults, as well as defining your own templates so you can quickly spin up preconfigured sites.

### Exploring the default config file

To get started, let's take a look at the default config file that got created when you initially ran `wpsites config`.

Here's the entirity of the default config file.

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

The config file is nothing more than a PHP file that returns an associative array. This associative array is where you can customize the options and define your own templates. While you will end up adding a bit more to it, it'll always follow this same simple structure.

There are three top-level properties. 

**First up is `sites_directory`.**

The value for this should be the path to an existing directory on your computer.

This directory is where WPSites will create new WordPress sites, so make sure the directory is being served up by whatever PHP server you're running.

If you're using Laravel Herd, the default value of `'$HOME/Herd'` should work great. If you're using MAMP PRO, you'll want to use `'$HOME/Sites'`.

**Next up is `defaults`.**

As you can see in the config file above, the value for `defaults` is an associative array.

On this array, you can define a set of default options that you want to apply to all sites you create. Keep in mind that any option can be set on `defaults` can also be overriden on an individual template.

The default config file defines the three options that are used to configure the database connection. These are `database_host`, `database_username`, and `database_password`. You may need to tweak these options to matching up with your local MySQL database server.

**Last up is `templates`**

The value for `templates` is an array of associated arrays.

Each item in the array represents a template that you can use when creating a new site. The only requirement for a template is that you give it a name by setting a value for `name`. Everything else is optional.

The default config file above has three templates. The first template is a barebones template that just define a name. The second template uses `enable_multisite` to create a WordPress multisite. The final template uses a few more options to customize the WordPress version, theme, and plugins.

Check out all the [template options](#template-options) below to see what's possible.

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

Right off the top you'll notice that there are no database options defined in `defaults`. Actually, there are no database options defined anywhere. In my case the default values for `database_host`, `database_username`, and `database_password` are the correct values for my connection. So defing the same values as defaults doesn't do anything at all. It's only present in the default config as it's something you might need to tweak and I wanted to make that as easy as possible.

Every support option has a default value. `enable_multisite` is false, `wordpress_version` is `'latest'`, and `enable_error_logging` is `true`. If you happy with the default value for an option, you don't need to configure it.

The one option I do have in `defaults` is `plugins`. Here I'm telling WPSites that I want the `code-snippets` plugin installed on every site. Don't worry if a template also defines `plugins`. These arrays are merged so the site ends up with the default plugins as well as the template specific plugins.

Below that are the 5 templates I've been using. I still keep around a basic site and a basic multisite, but the final three templates are all specific to my work on Independent Analytics.

IAWP Dev is a good template for my development site which comes a couple of symlinked plugins as well as a few third-party plugins that we integrate with. There's also IAWP Dev Multisite which is just a multisite version of this dev site.

Last up is IAWP Latest Stable Release. This is a basic site that install the lastest released version of Independent Analytics form the WordPress plugin repository. This template is useful when I want to recreate a customer bug or see how the behavior I'm changing originally worked.

# Template options

Every option can be set in one of two places. You can set it inside of `defaults` to serve as a default for all site templates, or you can set it inside of a site template in `templates` to have it be specific to just one template

Reorder these by most likley to be customizes. Themes and plugins first.

### WordPress version

Option: `wordpress_version`

Default: `'latest'`

The version of WordPress your site will use. Valid values include 'latest', 'nightly', or a WordPress version such as '6.0.0'.

### Database host

Option: `database_host`

Default: `'127.0.0.1:3306'`

The host (and port!) for your sites database connection.

### Database username

Option: `database_username`

Default: `'root'`

The username for your sites database connection.

### Database password

Option: `database_password`

Default: `null`

The password to for your sites database connection. A value of null should be used if there is no password.

### Database name

There is no option for defining the database name to create. Instead, the database name comes from the unique slug you pick when creating a WordPress site with `wpsites create`.

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

Enable automatically logging in to the admin panel. This is by installing and configuring the automatic-login plugin.

### Theme

Option: `theme`

Default: `'twentytwentyfour'`

The slug of the WordPress theme to use. This must be a theme available on the WordPress theme repository. Symlinked themes are currently not supported.

### Plugins

Option: `plugins`

Default: `[]`

An array of plugins to install.

Use a slug like `woocommerce` to install a plugin from the WordPress repository.

Use an absolute path like `/plugin/to/symlink` to symlink a local plugin on your machine.

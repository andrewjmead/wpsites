# WPSites

WPSites is the fastest way to create a localhost WordPress website.

Just run `wpsites create`, select a site template, and give your site a name. Boom. Done.

Check it out:

https://github.com/user-attachments/assets/b04fc4d8-7603-4729-b399-db08e4b1da6a

# Why?

I've been building [Independent Analytics](https://wordpress.org/plugins/independent-analytics/) for the last 2 years, and during that time I've created hundreds (thousands?) of temporary WordPress sites.

I'd create new sites for development. I'd create new sites to test customer issues. I'd create new sites to track down regression.

**It was the same process over and over again. I'd create a new site only to configure it exactly the same way as I'd done countless times before. Enable debug mode. Symlink the local build. Install the plugins we integrate with. The list goes on and on.**

No more.

Now I run `wpsites create`, select which of my templates I want to use, and wait 10 seconds as the new site is created.

It's sublime.

This has been the most genuily usefull project I've created. I hope you get some value out of it. If you do, [say hi](https://twitter.com/andrew_j_mead). I'd love to hear from you.

# Getting started

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

Amazing! Now creating your first site is just the beginning. Read on to learn how you can build your own templates to create sites specific to your needs!

# Configuring WPSites

### Starting from scratch

```php
<?php

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
    ],
];

```

### Defining defaults 

### Defining templates


---

# Building your own templates

There are three top-level properties in `.wpsites.php`. There's `sites_directory`, `defaults`, and `templates`. This section covers `defaults` and `templates`.

Let's start with `templates`.

The `templates` property defines an array of WordPress site templates. The default configuration file comes with a few predefined templates, but you'll definitely want to create and customize your own.

You can create a new template by adding an associative array to the end of `templates`. The only thing you have to define is a template name using `name`. All other properties are optional.

Your configuration file as contains `defaults`. `defaults` is an associative array where you can define all of the same properties for a template (except for `name`).

So if you wanted ever template to use a specific version of WordPress, such as `5.9.10`, you could define `'wordpress_version' => '5.9.10'` under `defaults` instead of needing to define it for every template you create. If there a specific site where you want to use the latest version of WordPress instead, you could set `'wordpress_version' => 'latest'` on the one site template to override the default value.

Let's see this in action. Below is the default `.wpsite.php` configuration file. Take a quick look and continue on.

```php
<?php  
  
return [  
    'sites_directory' => '$HOME/Herd',  
  
    'defaults' => [  
        'wordpress_version'      => 'latest',  
        'database_host'          => '127.0.0.1:3306',  
        'database_username'      => 'root',  
        'database_password'      => null,  
        'database_name'          => null,  
        'admin_username'         => 'admin',  
        'admin_email'            => 'admin@example.com',  
        'admin_password'         => 'password',  
        'enable_multisite'       => false,  
        'enable_error_logging'   => true,  
        'enable_automatic_login' => true,  
        'theme'                  => 'twentytwentyfour',  
        'plugins'                => []  
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
	        'name'    => 'Symlink plugin example',  
            'plugins' => [  
                '/path/to/symlink/folder',  
            ],
        ],
        [            
			'name'              => 'Bug recreation example',  
			'wordpress_version' => '5.9.10',
            'plugins'           => [  
                'independent-analytics',  
            ],
        ],
    ],
];
```

Under `defaults` is every option that you can define. All of these options can be defined on `defaults` and also defined on a site template.

You'll notice that the default value for `wordpress_version` is `latest`. The first 3 site templates don't override this value, so sites created with those templates will use the latest WordPress version.

The last site template overrides the default value for `wordpress_version` by settings it own version of `wordpress_version` to `5.9.10`.

So you can define a set of reasonable defaults and then override those values on a per template basis.

It's important to note that every default value in `defaults` above is the default value for the property. So in this case, the values in `defaults` are only there to show you all the options you can set. Since the default value for `wordpress_version` is already latest, it's a bit redundant. That means the config file above and the one below are actually the same.

```php
<?php  
  
return [  
    'sites_directory' => '$HOME/Herd',  
  
    'defaults' => [],  

    'templates' => [  
        [
            'name' => 'Basic WordPress',  
        ],
        [
            'name'             => 'Basic Multisite WordPress',  
            'enable_multisite' => true,  
        ],
        [            
	        'name'    => 'Symlink plugin example',  
            'plugins' => [  
                '/path/to/symlink/folder',  
            ],
        ],
        [            
			'name'              => 'Bug recreation example',  
			'wordpress_version' => '5.9.10',
            'plugins'           => [  
                'independent-analytics',  
            ],
        ],
    ],
];
```

While every option (except `name`) can be set as a default or on a site template, it doesn't always make sense to do so. A good example of this are the database connection options. It's likely that all the sites you create are going to use the same values. You might need to customize it from the defaults, but there's no need to do it on a per site basis copying the same values over and over.

# Template options

Every option can be set in one of two places. You can set it inside of `defaults` to serve as a default for all site templates, or you can set it inside of a site template in `templates` to have it be specific to just one template

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

# Things to keep in mind

1. WPSites uses WP-CLI behind the scenes
2. WPSites is not a server and doesn't care how your localhost sites are being served up

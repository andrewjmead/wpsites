<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sites Directory
    |--------------------------------------------------------------------------
    |
    | The path to the folder where your WordPress sites will be created.
    |
    */
    'sites_directory' => '$HOME/Herd',

    'defaults' => [

        /*
        |--------------------------------------------------------------------------
        | WordPress Version
        |--------------------------------------------------------------------------
        |
        | The version of WordPress your site will use. Valid values include 'latest',
        | 'nightly', or a WordPress version such as '6.0.0'.
        |
        */

        'wordpress_version' => 'latest',

        /*
        |--------------------------------------------------------------------------
        | Database Host
        |--------------------------------------------------------------------------
        |
        | The host (and port!) for your sites database connection.
        |
        */

        'database_host' => '127.0.0.1:3306',

        /*
        |--------------------------------------------------------------------------
        | Database Username
        |--------------------------------------------------------------------------
        |
        | The username for your sites database connection.
        |
        */

        'database_username' => 'root',

        /*
        |--------------------------------------------------------------------------
        | Database Password
        |--------------------------------------------------------------------------
        |
        | The password to for your sites database connection. A value of null should
        | be used if there is no password.
        |
        */

        'database_password' => null,

        /*
        |--------------------------------------------------------------------------
        | Database Name
        |--------------------------------------------------------------------------
        |
        | The name of the database to create. It doesn't make sense to set a
        | default value for the database name. Instead, it's recommend to
        | fall back to the default value which is the sites slug.
        |
        */

        'database_name' => null,

        /*
        |--------------------------------------------------------------------------
        | Admin Username
        |--------------------------------------------------------------------------
        |
        | The username for the admin user.
        |
        */

        'admin_username' => 'admin',

        /*
        |--------------------------------------------------------------------------
        | Admin Email
        |--------------------------------------------------------------------------
        |
        | The email for the admin user.
        |
        */

        'admin_email' => 'admin@example.com',

        /*
        |--------------------------------------------------------------------------
        | Admin Password
        |--------------------------------------------------------------------------
        |
        | The password for the admin user.
        |
        */

        'admin_password' => 'password',

        /*
        |--------------------------------------------------------------------------
        | Enable Multisite
        |--------------------------------------------------------------------------
        |
        | Enable to create a multisite. This will also create a second site as
        | part of the multisite network.
        |
        */

        'enable_multisite' => true,

        /*
        |--------------------------------------------------------------------------
        | Enable Error Logging
        |--------------------------------------------------------------------------
        |
        | Enable to setup error logging. This sets WP_DEBUG to true, WP_DEBUG_LOG
        | to true, and WP_DEBUG_DISPLAY to false.
        |
        */

        'enable_error_logging' => true,

        /*
        |--------------------------------------------------------------------------
        | Enable Automatic Login
        |--------------------------------------------------------------------------
        |
        | Enable automatically logging in to the admin panel. This is by installing
        | and configuring the automatic-login plugin.
        |
        */

        'enable_automatic_login' => true,

        /*
        |--------------------------------------------------------------------------
        | Theme
        |--------------------------------------------------------------------------
        |
        | The slug of the WordPress theme to use. This must be a theme available
        | on the WordPress theme repository.
        |
        */

        'theme' => 'twentytwentyfour',

        /*
        |--------------------------------------------------------------------------
        | Plugins
        |--------------------------------------------------------------------------
        |
        | An array of WordPress plugins to install. The plug must either be a plugin
        | available on the WordPress plugin repository, or an absolute path to a
        | local plugin folder to symlink.
        |
        */

        'plugins' => []
    ],

    /*
    |--------------------------------------------------------------------------
    | Templates
    |--------------------------------------------------------------------------
    |
    | This is where you get to have a bit of fun. Define an array of templates
    | representing the different WordPress sites you want to be able to
    | spin up.
    |
    | Every option documented under 'defaults' above can be overridden for every
    | template. In addition, all templates must define a template name which
    | is used as the WordPress sites name.
    |
    | It's worth mentioning that the default plugins and the template plugins
    | will be merged. So you can define plugins to use for every site, as
    | well as plugins to use only for a specific template.
    |
    | Using `wpsites create`, you'll be able to quickly spin up a version of
    | any of your templates.
    |
    */

    'templates' => [
        [
            'name' => 'Basic WordPress',
        ],
        [
            'name' => 'Basic Multisite WordPress',
            'enable_multisite' => true,
        ],
        [
            'name' => 'Symlink plugin example',
            'plugins' => [
                '/path/to/symlink/folder',
            ],
        ],
        [
            'name' => 'Repository plugin example',
            'plugins' => [
                'independent-analytics',
            ],
        ],
    ],
];

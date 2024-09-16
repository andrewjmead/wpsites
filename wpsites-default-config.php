<?php

// Getting started? You'll need to change a few values below. Checkout the docs here:
// https://github.com/andrewjmead/wpsites#configure-your-sites-directory

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
            'theme' => 'twentytwentythree',
        ],
    ],
];

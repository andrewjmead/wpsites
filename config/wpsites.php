<?php

// TODO - Need to support multisite

return [
    'sites_directory' => '~/Herd',
    'defaults'        => [
        'wordpress_version' => 'nightly',
        // Default: 'latest', '6.0.0', 'nightly'

        'database_host' => '127.0.0.1:3306',
        // Default: '127.0.0.1'
        // 'database_username' => null, // Default: 'root'
        // 'database_password' => null, // Default: no password
        // 'database_name'     => null, // While you can set a default value for this option, it's recommend to use the default site slug or define in separately for each site

        // 'admin_username' => null, // Default: 'admin'
        // 'admin_password' => null, // Default: 'password'
        // 'admin_email'    => null, // Default: 'admin@example.com'

        'enable_error_logging' => true,
        // Default: true
        'enable_automatic_login' => true,
        // Default: true

        // 'theme' => null, // Default: 'twentytwentyfour'

        // 'plugins' => null, // Default: null
    ],
    'templates' => [
        [
            'name' => 'Basic WordPress',
        ],
        [
            'name'             => 'Basic multisite WordPress',
            'enable_multisite' => true,
        ],
        [
            'name'    => 'Symlink plugin example',
            'plugins' => [
                '/path/to/symlink/folder',
            ],
        ],
        [
            'name'    => 'Repository plugin example',
            'plugins' => [
                'independent-analytics',
            ],
        ],
    ],
];

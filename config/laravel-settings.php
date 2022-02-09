<?php

return [

    'table' => 'settings',

    'cache' => [
        'ttl' => 3600
    ],

    'encryption' => [
        'default' => false
    ],

    'settings' => [

    ],

    'aliases' => [
        // 'siteName' => \My\Settings\SiteName::class
    ],

    'routes' => [
        'enabled' => true,
        'prefix' => 'settings',
        'middleware' => []
    ],

    'js' => [
        'autoload' => [
            // Settings to always load
        ]
    ]

];

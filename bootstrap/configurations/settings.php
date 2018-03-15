<?php

$configurations = [
    'settings' => [
        'addContentLengthHeader' => false,
        'displayErrorDetails' => true, #TODO: false on production
        'determineRouteBeforeAppMiddleware' => true,
        'db' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'vote',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => ''
        ]
    ]
];


<?php

/*
 * config file for storaging files
 *
 * LocalDriver is available
 */
return [
    'drivers' => [
        'Local' => [
            'basePath' => '/storage',
        ],
    ],

    'locations' => [
        'image' => [
            'driver' => 'Local',
            'path' => '/image',
        ],
    ],
];

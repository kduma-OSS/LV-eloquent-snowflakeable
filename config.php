<?php

return [
    'start_date' => '2023-12-29',

    'resolver' => [
        // cache, file, random
        'driver' => 'file',

        'file' => [
            'lock_file_dir' => storage_path('framework/snowflake'),
        ],

        'cache' => [
            'store' => null,
            'prefix' => 'snowflake_',
        ],
    ],
];

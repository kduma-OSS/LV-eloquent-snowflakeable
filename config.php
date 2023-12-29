<?php

return [
    'start_date' => '2023-12-29',

    // snowflake, sonyflake
    'variant' => 'snowflake',

    'variants' => [
        'snowflake' => [
            'datacenter' => 0,
            'worker_id' => 0,
        ],

        'sonyflake' => [
            'machine_id' => 0,
        ],
    ],

    // cache, file, random
    'resolver' => 'random',

    'resolvers' => [
        'file' => [
            'lock_file_dir' => storage_path('framework/snowflake'),
        ],

        'cache' => [
            'store' => null,
            'prefix' => 'snowflake_',
        ],
    ],
];

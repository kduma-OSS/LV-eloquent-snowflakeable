<?php

return [
    'start_timestamp' => strtotime(env('SNOWFLAKE_START_DATE', '2019-08-08 08:08:08')) * 1000,

    // snowflake, sonyflake
    'variant' => env('SNOWFLAKE_VARIANT', 'snowflake'),

    'variants' => [
        'snowflake' => [
            'datacenter' => env('SNOWFLAKE_DATACENTER_ID', 0),
            'worker_id' => env('SNOWFLAKE_WORKER_ID', 0),
        ],

        'sonyflake' => [
            'machine_id' =>  env('SNOWFLAKE_WORKER_ID', 0),
        ],
    ],

    // cache, file, random
    'resolver' => env('SNOWFLAKE_RESOLVER', 'random'),

    'resolvers' => [
        'file' => [
            'lock_file_dir' => env('SNOWFLAKE_FILE_RESOLVER_LOCK_DIR', storage_path('framework/snowflake')),
        ],

        'cache' => [
            'store' => env('SNOWFLAKE_CACHE_RESOLVER_STORE', null),
            'prefix' => env('SNOWFLAKE_CACHE_RESOLVER_PREFIX', 'snowflake_'),
        ],
    ],
];

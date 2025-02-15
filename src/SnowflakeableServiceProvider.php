<?php

namespace KDuma\Eloquent;

use Godruoyi\Snowflake\FileLockResolver;
use Godruoyi\Snowflake\IdGenerator;
use Godruoyi\Snowflake\LaravelSequenceResolver;
use Godruoyi\Snowflake\RandomSequenceResolver;
use Godruoyi\Snowflake\SequenceResolver;
use Godruoyi\Snowflake\Sonyflake;
use Illuminate\Contracts\Cache\Factory;
use Godruoyi\Snowflake\Snowflake;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class SnowflakeableServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SequenceResolver::class, function (Application $app) {
            return match (config('snowflake.resolver', 'file')) {
                'cache' => (new LaravelSequenceResolver(
                    cache: $app->make(Factory::class)->store(
                        name: config('snowflake.resolvers.cache.store')
                    )
                ))->setCachePrefix(
                    prefix: config('snowflake.resolvers.cache.prefix', 'snowflake_')
                ),

                'file' => new FileLockResolver(
                    lockFileDir: config('snowflake.resolvers.file.lock_file_dir', storage_path('framework/snowflake'))
                ),

                default => new RandomSequenceResolver()
            };
        });
        $this->app->singleton(IdGenerator::class, function (Application $app) {
            $snowflake = match (config('snowflake.variant', 'snowflake')) {
                default => new Snowflake(
                    datacenter: (int) config('snowflake.variants.snowflake.datacenter', 0),
                    workerId: (int) config('snowflake.variants.snowflake.worker_id', 0)
                ),

                'sonyflake' => new Sonyflake(
                    machineId: (int) config('snowflake.variants.sonyflake.machine_id', 0)
                ),
            };

            $start_timestamp = config('snowflake.start_date');
            if($start_timestamp) {
                $snowflake->setStartTimeStamp($start_timestamp);
            }
            $snowflake->setSequenceResolver($app->make(SequenceResolver::class));

            return $snowflake;
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config.php' => config_path('snowflake.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/../config.php', 'snowflake');
    }
}

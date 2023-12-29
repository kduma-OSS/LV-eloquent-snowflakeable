<?php

namespace KDuma\Eloquent;

use Godruoyi\Snowflake\FileLockResolver;
use Godruoyi\Snowflake\LaravelSequenceResolver;
use Godruoyi\Snowflake\RandomSequenceResolver;
use Godruoyi\Snowflake\SequenceResolver;
use Illuminate\Contracts\Cache\Factory;
use KDuma\Eloquent\Internal\Snowflake;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class SnowflakeableServiceProvider extends ServiceProvider
{
    public const DEFAULT_START_DATE = '2023-12-29';

    public function register(): void
    {
        $this->app->singleton(SequenceResolver::class, function (Application $app) {
            return match (config('snowflake.resolver.driver', 'file')) {
                'cache' => (new LaravelSequenceResolver(
                    cache: $app->make(Factory::class)->store(
                        name: config('snowflake.resolver.cache.store')
                    )
                ))->setCachePrefix(
                    prefix: config('snowflake.resolver.cache.prefix', 'snowflake_')
                ),

                'file' => new FileLockResolver(
                    lockFileDir: config('snowflake.resolver.file.lock_file_dir', storage_path('framework/snowflake'))
                ),

                default => new RandomSequenceResolver()
            };
        });
        $this->app->singleton(Snowflake::class, function (Application $app) {
            $snowflake = new Snowflake(
                datacenter: (int) config('snowflake.datacenter', 0),
                workerId: (int) config('snowflake.worker_id', 0)
            );

            $snowflake->setStartTimeStamp(strtotime(config('snowflake.start_date', self::DEFAULT_START_DATE))*1000);
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

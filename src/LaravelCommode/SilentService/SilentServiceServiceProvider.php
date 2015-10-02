<?php

namespace LaravelCommode\SilentService;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class SilentServiceServiceProvider extends ServiceProvider
{
    const PROVIDES_MANAGER = 'commode.silent-service.manager';

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(self::PROVIDES_MANAGER, function (Application $app) {
            return new SilentManager($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [self::PROVIDES_MANAGER, SilentManager::class];
    }
}

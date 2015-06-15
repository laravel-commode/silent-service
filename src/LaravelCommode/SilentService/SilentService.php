<?php
namespace LaravelCommode\SilentService;

use Illuminate\Support\ServiceProvider;

abstract class SilentService extends ServiceProvider
{
    /**
     * This method will be triggered instead
     * of original ServiceProvider::register().
     * @return mixed
     */
    abstract public function registering();


    /**
     * This method will be triggered instead
     * when application's booting event is fired.
     * @return mixed
     */
    abstract public function launching();

    /**
     * Method is supposed to return an array of service providers'
     * class names, which this service is dependent on.
     * All providers will be registered before
     * SilentService::registering() is fired.
     * @return string[]
     */
    protected function uses()
    {
        return [];
    }

    /**
     * Method is supposed to return an array of aliases,
     * which this service provider is supposed to provide/register.
     * return string[]
     */
    protected function aliases()
    {
        return [];
    }

    /**
     * @param array $resolvable
     * @param callable $do
     */
    protected function with(array $resolvable, callable $do)
    {
        $resolved = [];

        foreach ($resolvable as $resolvableName) {
            $resolved[] = $this->app->make($resolvableName);
        }

        call_user_func_array($do, $resolved);
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if (!$this->app->bound(SilentServiceServiceProvider::PROVIDES_MANAGER)) {
            $this->app->register(SilentServiceServiceProvider::class);
        }

        $this->with([SilentServiceServiceProvider::PROVIDES_MANAGER], function (SilentManager $manager) {
            $manager->registerServices($this->uses());
        });

        foreach ($this->aliases() as $alias => $target) {
            $this->app->alias($alias, $target);
        }

        $this->app->booting([$this, 'launching']);

        $this->registering();
    }
}

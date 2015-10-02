<?php

namespace LaravelCommode\SilentService;

use Illuminate\Contracts\Foundation\Application;

/**
 * Class SilentManager.
 */
class SilentManager
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    private $application;

    /**
     * @var string[]
     */
    private $loaded = [];

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function registerServices(array $providers)
    {
        return $this->launchServices($this->differUnique($providers));
    }

    public function registerService($serviceName)
    {
        return $this->registerServices([$serviceName]);
    }

    private function differUnique(array $serviceList)
    {
        return array_diff($serviceList, $this->application->getLoadedProviders());
    }

    private function launchServices(array $services)
    {
        foreach ($services as $service) {
            $this->loaded[] = $service;
            $this->application->registerDeferredProvider($service);
        }

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLoaded()
    {
        return $this->loaded;
    }
}

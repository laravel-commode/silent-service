<?php

namespace LaravelCommode\SilentService;

use Illuminate\Foundation\Application;

/**
 * Class SilentManager.
 */
class SilentManager
{
    /**
     * @var \Illuminate\Foundation\Application
     */
    private $application;

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
            $this->application->registerDeferredProvider($service);
        }

        return $this;
    }
}

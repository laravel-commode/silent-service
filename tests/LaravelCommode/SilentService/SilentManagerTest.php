<?php

namespace LaravelCommode\SilentService;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\App;
use PHPUnit_Framework_MockObject_MockObject as Mock;

class SilentManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application|Mock
     */
    private $application;

    /**
     * @var SilentManager
     */
    private $testInstance;

    protected function setUp()
    {
        $this->application = $this->getMock(
            'Illuminate\Foundation\Application',
            ['getLoadedProviders', 'registerDeferredProvider']
        );

        $this->testInstance = new SilentManager($this->application);

        parent::setUp();
    }

    public function testRegistrations()
    {
        $firstRegister = ['Service1', 'Service2'];

        $this->application->expects($this->any())->method('getLoadedProviders')
            ->will($this->returnCallback(function () use ($firstRegister) {
                return [$firstRegister[0]];
            }));

        $this->testInstance->registerServices($firstRegister);
    }

    public function testRegistration()
    {
        $firstRegister = ['Service1', 'Service2'];
        $thenRegister = 'Service3';


        $this->application->expects($this->any())->method('getLoadedProviders')
            ->will($this->returnCallback(function () use ($firstRegister) {
                return $firstRegister;
            }));

        $this->testInstance->registerService($thenRegister);
    }

    protected function tearDown()
    {
        unset($this->application);
        unset($this->testInstance);
        parent::tearDown();
    }
}

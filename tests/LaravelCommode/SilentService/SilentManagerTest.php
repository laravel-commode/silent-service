<?php

namespace LaravelCommode\SilentService;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\App;
use PHPUnit_Framework_MockObject_MockObject as Mock;

class SilentManagerTest extends TestAbstraction
{

    /**
     * @var SilentManager
     */
    private $testInstance;

    protected function applicationMocksMethods()
    {
        return ['getLoadedProviders'];
    }

    protected function setUp()
    {
        parent::setUp();
        $this->testInstance = new SilentManager($this->getApplicationMock());
    }

    public function testRegistrations()
    {
        $firstRegister = ['Service1', 'Service2'];

        $this->getApplicationMock()->expects($this->any())->method('getLoadedProviders')
            ->will($this->returnCallback(function () use ($firstRegister) {
                return [$firstRegister[0] => true];
            }));

        $this->testInstance->registerServices($firstRegister);

        $this->assertSame($firstRegister[1], $this->testInstance->getLoaded()[0]);
    }

    public function testRegistration()
    {
        $firstRegister = ['Service1' => true, 'Service2' => true];
        $thenRegister = 'Service3';


        $this->getApplicationMock()->expects($this->any())->method('getLoadedProviders')
            ->will($this->returnCallback(function () use ($firstRegister) {
                return $firstRegister;
            }));

        $this->testInstance->registerService($thenRegister);

        $this->assertContains($thenRegister, $this->testInstance->getLoaded());
    }

    protected function tearDown()
    {
        unset($this->testInstance);
        parent::tearDown();
    }
}

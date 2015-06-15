<?php

namespace LaravelCommode\SilentService;

use PHPUnit_Framework_MockObject_MockObject as Mock;

class SilentServiceServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Illuminate\Foundation\Application|Mock
     */
    private $application;

    /**
     * @var SilentServiceServiceProvider
     */
    private $testInstance;

    protected function setUp()
    {
        $this->application = $this->getMock('Illuminate\Foundation\Application', ['singleton']);
        $this->testInstance = new SilentServiceServiceProvider($this->application);
        parent::setUp();
    }

    public function testRegistering()
    {
        $this->application->expects($this->atLeastOnce())->method('singleton')
            ->with(SilentServiceServiceProvider::PROVIDES_MANAGER, $this->callback(function ($callable) {
                return $callable($this->application) instanceof SilentManager;
            }));

        $this->testInstance->register();
    }

    protected function tearDown()
    {
        unset($this->testInstance);
        unset($this->application);
        parent::tearDown();
    }
}

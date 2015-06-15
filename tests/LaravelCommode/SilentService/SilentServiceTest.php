<?php

namespace LaravelCommode\SilentService;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as Mock;

class SilentServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Illuminate\Foundation\Application|Mock
     */
    private $application;

    /**
     * @var SilentService|Mock
     */
    private $testInstance;

    /**
     * @var SilentManager
     */
    private $silentManager;

    protected function setUp()
    {
        $this->application = $this->getMock(
            'Illuminate\Foundation\Application',
            ['singleton', 'bound', 'register', 'make', 'getLoadedProviders', 'booting', 'alias']
        );

        $this->testInstance = $this->getMockForAbstractClass(
            SilentService::class,
            [$this->application],
            '',
            true,
            true,
            true,
            ['aliases']
        );

        $this->silentManager = new SilentManager($this->application);

        parent::setUp();
    }

    public function testRegister()
    {
        $this->application->expects($this->atLeastOnce())->method('bound')
            ->with(SilentServiceServiceProvider::PROVIDES_MANAGER)
            ->will($this->returnValue(false));

        $this->application->expects($this->atLeastOnce())->method('register')
            ->with(SilentServiceServiceProvider::class);

        $this->application->expects($this->any())->method('make')
            ->will($this->returnCallback(function ($make) {
                switch($make)
                {
                    case SilentServiceServiceProvider::PROVIDES_MANAGER:
                        return $this->silentManager;
                    default:
                        var_dump($make);
                        die;
                }
            }));

        $this->application->expects($this->any())->method('getLoadedProviders')
            ->will($this->returnValue([]));

        list($testAliasKey, $testAliasValue) = [uniqid(), uniqid()];

        $this->testInstance->expects($this->atLeastOnce())->method('aliases')
            ->will($this->returnValue([$testAliasKey => $testAliasValue]));

        $this->application->expects($this->atLeastOnce())->method('alias')
            ->with($testAliasKey, $testAliasValue);

        $this->testInstance->register();
    }

    public function testEmptyUsesAliases()
    {
        $methodList = ['uses', 'aliases'];

        foreach ($methodList as $methodName) {
            $reflectionMethod = new \ReflectionMethod(SilentService::class, $methodName);
            $reflectionMethod->setAccessible(true);
            $this->assertTrue(is_array($reflectionMethod->invoke($this->testInstance)));
        }
    }

    protected function tearDown()
    {
        unset($this->testInstance);
        unset($this->silentManager);
        unset($this->application);
        parent::tearDown();
    }
}

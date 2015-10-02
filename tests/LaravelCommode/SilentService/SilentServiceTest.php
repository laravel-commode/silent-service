<?php

namespace LaravelCommode\SilentService;

use Illuminate\Foundation\AliasLoader;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as Mock;

class SilentServiceTest extends TestAbstraction
{
    /**
     * @var SilentService|Mock
     */
    private $testInstance;

    /**
     * @var SilentManager
     */
    private $silentManager;

    protected function applicationMocksMethods()
    {
        return ['getLoadedProviders'];
    }

    protected function setUp()
    {
        parent::setUp();

        $this->testInstance = $this->getMockForAbstractClass(
            SilentService::class,
            [$this->getApplicationMock()],
            '',
            true,
            true,
            true,
            ['aliases']
        );

        $this->silentManager = new SilentManager($this->getApplicationMock());

    }

    public function testRegister()
    {
        $this->getApplicationMock()
            ->expects($this->atLeastOnce())->method('bound')
            ->with(SilentServiceServiceProvider::PROVIDES_MANAGER)
            ->will($this->returnValue(false));

        $this->getApplicationMock()->expects($this->atLeastOnce())->method('register')
            ->with(SilentServiceServiceProvider::class);

        $this->getApplicationMock()->expects($this->any())->method('make')
            ->will($this->returnCallback(function ($make) {
                switch ($make) {
                    case SilentServiceServiceProvider::PROVIDES_MANAGER:
                        return $this->silentManager;
                }
            }));

        $this->getApplicationMock()->expects($this->any())->method('getLoadedProviders')
            ->will($this->returnValue([]));

        list($testAliasKey, $testAliasValue) = [uniqid('Alias'), uniqid('Facade')];

        $this->testInstance->expects($this->atLeastOnce())->method('aliases')
            ->will($this->returnValue([$testAliasKey => $testAliasValue]));

        $this->getApplicationMock()->expects($this->once())->method('booting')
            ->will($this->returnCallback(function (callable $callback) {
                $callback();
            }));

        /**
         * @var AliasLoader|Mock $aliasLoaderMock
         */
        $aliasLoaderMock = $this->getMock(AliasLoader::class, [], [[]], '');

        AliasLoader::setInstance($aliasLoaderMock);
        $aliasLoaderMock->setAliases([]);
        $aliasLoaderMock->expects($this->any())->method('getAliases')
            ->will($this->returnValue([]));

        $aliasLoaderMock->expects($this->once())->method('alias')
            ->with($testAliasKey, $testAliasValue);

        $this->testInstance->register();
    }

    public function testEmptyUsesAliases()
    {
        $methodList = ['uses', 'aliases'];

        foreach ($methodList as $methodName) {
            $reflectionMethod = new \ReflectionMethod(SilentService::class, $methodName);
            $reflectionMethod->setAccessible(true);
            $reflectionMethod->invoke($this->testInstance);
        }
    }

    protected function tearDown()
    {
        unset($this->testInstance);
        unset($this->silentManager);
        parent::tearDown();
    }
}

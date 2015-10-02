<?php namespace LaravelCommode\SilentService;

class SilentServiceServiceProviderTest extends TestAbstraction
{

    /**
     * @var SilentServiceServiceProvider
     */
    private $testInstance;

    protected function setUp()
    {
        parent::setUp();
        $this->testInstance = new SilentServiceServiceProvider($this->getApplicationMock());
    }

    public function testRegistering()
    {
        $this->getApplicationMock()->expects($this->atLeastOnce())->method('singleton')
            ->with(SilentServiceServiceProvider::PROVIDES_MANAGER, $this->callback(function ($callable) {
                return $callable($this->getApplicationMock()) instanceof SilentManager;
            }));

        $this->testInstance->register();
    }

    public function testProvides()
    {
        $this->assertSame(
            [SilentServiceServiceProvider::PROVIDES_MANAGER, SilentManager::class],
            $this->testInstance->provides()
        );
    }

    protected function tearDown()
    {
        unset($this->testInstance);
        parent::tearDown();
    }
}

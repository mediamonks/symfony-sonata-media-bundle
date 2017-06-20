<?php

namespace MediaMonks\SonataMediaBundle\Tests\Unit\Provider;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\Provider\ProviderInterface;
use MediaMonks\SonataMediaBundle\Provider\ProviderPool;
use MediaMonks\SonataMediaBundle\Tests\Unit\MockeryTrait;
use Mockery as m;

class ProviderPoolTest extends \PHPUnit_Framework_TestCase
{
    use MockeryTrait;

    public function testGetProvider()
    {
        $provider = $this->getProviderMock();
        $providerPool = new ProviderPool();

        $providerPool->addProvider($provider);
        $this->assertEquals($providerPool->getProvider('Test'), $provider);
    }

    public function testAddProvider()
    {
        $provider = $this->getProviderMock();
        $providerPool = new ProviderPool();

        $this->assertEquals(0, count($providerPool->getProviders()));
        $providerPool->addProvider($provider);
        $this->assertEquals(1, count($providerPool->getProviders()));
        $this->assertEquals($provider, $providerPool->getProviders()['Test']);
    }

    public function testAddProviders()
    {
        $provider = $this->getProviderMock();
        $provider2 = $this->getProviderMock('Test 2');

        $providerPool = new ProviderPool();
        $providerPool->addProviders([$provider, $provider2]);

        $this->assertEquals($provider, $providerPool->getProviders()['Test']);
        $this->assertEquals($provider2, $providerPool->getProviders()['Test 2']);
        $this->assertEquals(2, count($providerPool->getProviders()));
    }

    public function testSetProviders()
    {
        $provider = $this->getProviderMock();
        $provider2 = $this->getProviderMock('Test 2');

        $providerPool = new ProviderPool();
        $providerPool->setProviders([$provider, $provider2]);

        $this->assertEquals($provider, $providerPool->getProviders()['Test']);
        $this->assertEquals($provider2, $providerPool->getProviders()['Test 2']);
        $this->assertEquals(2, count($providerPool->getProviders()));

        $providerPool->setProviders([$provider, $provider2]);
        $this->assertEquals(2, count($providerPool->getProviders()));
    }

    public function testGetByMedia()
    {
        $media = m::mock(MediaInterface::class);
        $media->shouldReceive('getProvider')->once()->andReturn('Test');

        $provider = $this->getProviderMock();

        $providerPool = new ProviderPool();
        $providerPool->addProvider($provider);

        $this->assertEquals($provider, $providerPool->getByMedia($media));
    }

    public function testGetUnknownProvider()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        $providerPool = new ProviderPool();
        $providerPool->getProvider('Test');
    }

    /**
     * @param string $name
     * @return ProviderInterface
     */
    private function getProviderMock($name = 'Test')
    {
        $provider = m::mock(ProviderInterface::class);
        $provider->shouldReceive('getName')->andReturn($name);

        return $provider;
    }
}

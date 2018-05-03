<?php

namespace MediaMonks\SonataMediaBundle\Tests\Unit\Provider;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\Provider\ProviderInterface;
use MediaMonks\SonataMediaBundle\Provider\ProviderPool;
use MediaMonks\SonataMediaBundle\Tests\Unit\MockeryTrait;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class ProviderPoolTest extends TestCase
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
        $this->expectException(\InvalidArgumentException::class);

        $providerPool = new ProviderPool();
        $providerPool->getProvider('Test');
    }

    public function testGetProvidersByTypes()
    {
        $providerPool = new ProviderPool();
        $providerPool->addProviders(
            [
                $this->getProviderMock('Image 1'),
                $this->getProviderMock('Image 2'),
                $this->getProviderMock('Video 1', 'video'),
                $this->getProviderMock('Video 2', 'video')
            ]
        );

        $imageProviders = $providerPool->getProvidersByTypes(['image']);
        $this->assertCount(2, $imageProviders);
        $this->assertArrayHasKey('Image 1', $imageProviders);
        $this->assertArrayHasKey('Image 2', $imageProviders);

        $this->assertCount(4, $providerPool->getProvidersByTypes(['image', 'video']));
        $this->assertCount(2, $providerPool->getProvidersByTypes(['foo', 'video']));
        $this->assertCount(0, $providerPool->getProvidersByTypes(['foo']));
    }

    /**
     * @param string $name
     * @param string $type
     * @return ProviderInterface
     */
    private function getProviderMock($name = 'Test', $type = 'image')
    {
        $provider = m::mock(ProviderInterface::class);
        $provider->shouldReceive('getName')->andReturn($name);
        $provider->shouldReceive('getType')->andReturn($type);

        return $provider;
    }
}

<?php

namespace MediaMonks\SonataMediaBundle\Tests\Provider;

use MediaMonks\SonataMediaBundle\Exception\InvalidProviderUrlException;
use MediaMonks\SonataMediaBundle\Provider\AbstractProvider;
use MediaMonks\SonataMediaBundle\Provider\SoundCloudProvider;

class SouncloudProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testParseProviderReference()
    {
        $youtube = new SoundCloudProvider();
        $this->assertEquals('mediamonks-2/old-spice-workout-anthem', $youtube->parseProviderReference('https://soundcloud.com/mediamonks-2/old-spice-workout-anthem'));
        $this->assertEquals('mediamonks-2/old-spice-workout-anthem', $youtube->parseProviderReference('mediamonks-2/old-spice-workout-anthem'));
    }

    public function testParseInvalidProviderReference()
    {
        $this->setExpectedException(InvalidProviderUrlException::class);
        $youtube = new SoundCloudProvider();
        $youtube->parseProviderReference('https://soundcloud.com/');
    }

    public function testSupports()
    {
        $provider = new SoundCloudProvider();
        $this->assertFalse($provider->supports(AbstractProvider::SUPPORT_DOWNLOAD));
        $this->assertTrue($provider->supports(AbstractProvider::SUPPORT_EMBED));
        $this->assertTrue($provider->supports(AbstractProvider::SUPPORT_IMAGE));
        $this->assertFalse($provider->supports('foo'));
    }
}

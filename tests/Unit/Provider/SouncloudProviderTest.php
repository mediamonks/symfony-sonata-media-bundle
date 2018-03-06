<?php

namespace MediaMonks\SonataMediaBundle\Tests\Unit\Provider;

use MediaMonks\SonataMediaBundle\Exception\InvalidProviderUrlException;
use MediaMonks\SonataMediaBundle\Provider\AbstractProvider;
use MediaMonks\SonataMediaBundle\Provider\SoundCloudProvider;
use PHPUnit\Framework\TestCase;

class SouncloudProviderTest extends TestCase
{
    public function testParseProviderReference()
    {
        $youtube = new SoundCloudProvider();
        $this->assertEquals('mediamonks-2/old-spice-workout-anthem', $youtube->parseProviderReference('https://soundcloud.com/mediamonks-2/old-spice-workout-anthem'));
        $this->assertEquals('mediamonks-2/old-spice-workout-anthem', $youtube->parseProviderReference('mediamonks-2/old-spice-workout-anthem'));
    }

    public function testParseInvalidProviderReference()
    {
        $this->expectException(InvalidProviderUrlException::class);
        $youtube = new SoundCloudProvider();
        $youtube->parseProviderReference('https://soundcloud.com/');
    }
}

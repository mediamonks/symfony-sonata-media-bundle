<?php

namespace MediaMonks\SonataMediaBundle\Tests\Provider;

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
        $this->setExpectedException(\Exception::class);
        $youtube = new SoundCloudProvider();
        $youtube->parseProviderReference('https://soundcloud.com/');
    }
}

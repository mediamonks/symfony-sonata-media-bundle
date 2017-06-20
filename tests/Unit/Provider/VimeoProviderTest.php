<?php

namespace MediaMonks\SonataMediaBundle\Tests\Unit\Provider;

use MediaMonks\SonataMediaBundle\Exception\InvalidProviderUrlException;
use MediaMonks\SonataMediaBundle\Provider\AbstractProvider;
use MediaMonks\SonataMediaBundle\Provider\VimeoProvider;

class VimeoProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testParseProviderReference()
    {
        $youtube = new VimeoProvider();
        $this->assertEquals('184376204', $youtube->parseProviderReference('https://vimeo.com/184376204'));
        $this->assertEquals('184376204', $youtube->parseProviderReference('184376204'));
    }

    public function testParseInvalidProviderReference()
    {
        $this->setExpectedException(InvalidProviderUrlException::class);
        $youtube = new VimeoProvider();
        $youtube->parseProviderReference('https://vimeo.com/');
    }

    public function testParseInvalidProviderReference2()
    {
        $this->setExpectedException(InvalidProviderUrlException::class);
        $youtube = new VimeoProvider();
        $youtube->parseProviderReference('https://vimeo.com/foobar');
    }

    public function testSupports()
    {
        $provider = new VimeoProvider();
        $this->assertFalse($provider->supports(AbstractProvider::SUPPORT_DOWNLOAD));
        $this->assertTrue($provider->supports(AbstractProvider::SUPPORT_EMBED));
        $this->assertTrue($provider->supports(AbstractProvider::SUPPORT_IMAGE));
        $this->assertFalse($provider->supports('foo'));
    }
}

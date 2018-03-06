<?php

namespace MediaMonks\SonataMediaBundle\Tests\Unit\Provider;

use MediaMonks\SonataMediaBundle\Exception\InvalidProviderUrlException;
use MediaMonks\SonataMediaBundle\Provider\AbstractProvider;
use MediaMonks\SonataMediaBundle\Provider\VimeoProvider;
use PHPUnit\Framework\TestCase;

class VimeoProviderTest extends TestCase
{
    public function testParseProviderReference()
    {
        $youtube = new VimeoProvider();
        $this->assertEquals('184376204', $youtube->parseProviderReference('https://vimeo.com/184376204'));
        $this->assertEquals('184376204', $youtube->parseProviderReference('184376204'));
    }

    public function testParseInvalidProviderReference()
    {
        $this->expectException(InvalidProviderUrlException::class);
        $youtube = new VimeoProvider();
        $youtube->parseProviderReference('https://vimeo.com/');
    }

    public function testParseInvalidProviderReference2()
    {
        $this->expectException(InvalidProviderUrlException::class);
        $youtube = new VimeoProvider();
        $youtube->parseProviderReference('https://vimeo.com/foobar');
    }
}

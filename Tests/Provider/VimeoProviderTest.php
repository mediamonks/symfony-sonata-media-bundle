<?php

namespace MediaMonks\SonataMediaBundle\Tests\Provider;

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
        $this->setExpectedException(\Exception::class);
        $youtube = new VimeoProvider();
        $youtube->parseProviderReference('https://vimeo.com/');
    }

    public function testParseInvalidProviderReference2()
    {
        $this->setExpectedException(\Exception::class);
        $youtube = new VimeoProvider();
        $youtube->parseProviderReference('https://vimeo.com/foobar');
    }
}

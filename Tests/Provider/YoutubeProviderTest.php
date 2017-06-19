<?php

namespace MediaMonks\SonataMediaBundle\Tests\Provider;

use MediaMonks\SonataMediaBundle\Exception\InvalidProviderUrlException;
use MediaMonks\SonataMediaBundle\Provider\YouTubeProvider;

class YoutubeProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testParseProviderReference()
    {
        $youtube = new YouTubeProvider();
        $this->assertEquals('uN2nqdRLhQ8', $youtube->parseProviderReference('https://www.youtube.com/watch?v=uN2nqdRLhQ8'));
        $this->assertEquals('uN2nqdRLhQ8', $youtube->parseProviderReference('https://www.youtube.com/watch?v=uN2nqdRLhQ8&foo=bar'));
        $this->assertEquals('uN2nqdRLhQ8', $youtube->parseProviderReference('https://youtu.be/uN2nqdRLhQ8'));
        $this->assertEquals('uN2nqdRLhQ8', $youtube->parseProviderReference('https://youtu.be/uN2nqdRLhQ8?foo=bar'));
        $this->assertEquals('uN2nqdRLhQ8', $youtube->parseProviderReference('uN2nqdRLhQ8'));
    }

    public function testParseInvalidProviderReference()
    {
        $this->setExpectedException(InvalidProviderUrlException::class);
        $youtube = new YouTubeProvider();
        $youtube->parseProviderReference('https://www.youtube.com/watch?foo=bar');
    }

    public function testParseInvalidProviderReference2()
    {
        $this->setExpectedException(InvalidProviderUrlException::class);
        $youtube = new YouTubeProvider();
        $youtube->parseProviderReference('https://www.youtube.com');
    }

    public function testParseInvalidProviderReferenceShort()
    {
        $this->setExpectedException(InvalidProviderUrlException::class);
        $youtube = new YouTubeProvider();
        $youtube->parseProviderReference('https://youtu.be');
    }
}

<?php

namespace MediaMonks\SonataMediaBundle\Tests\Unit\ParameterBag;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\ParameterBag\DownloadParameterBag;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use Mockery as m;

class ImageParameterBagTest extends \PHPUnit_Framework_TestCase
{
    public function testGettersSetters()
    {
        $media = m::mock(MediaInterface::class);
        $media->shouldReceive('getId')->andReturn(1);

        $bag = new ImageParameterBag(400, 300);
        $this->assertEquals(['id' => 1, 'width' => 400, 'height' => 300], $bag->toArray($media));
        $bag->setExtra(['foo' => 'bar']);
        $this->assertEquals(['id' => 1, 'width' => 400, 'height' => 300, 'foo' => 'bar'], $bag->toArray($media));
        $this->assertFalse($bag->hasExtra('bar'));
        $bag->addExtra('bar', 'baz');
        $this->assertTrue($bag->hasExtra('bar'));
        $this->assertEquals(['id' => 1, 'width' => 400, 'height' => 300, 'foo' => 'bar', 'bar' => 'baz'], $bag->toArray($media));
        $bag->removeExtra('bar');
        $this->assertFalse($bag->hasExtra('bar'));
        $this->assertEquals(['id' => 1, 'width' => 400, 'height' => 300, 'foo' => 'bar'], $bag->toArray($media));
    }
}

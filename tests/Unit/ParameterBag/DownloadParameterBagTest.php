<?php

namespace MediaMonks\SonataMediaBundle\Tests\Unit\ParameterBag;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\ParameterBag\MediaParameterBag;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class DownloadParameterBagTest extends TestCase
{
    public function testGettersSetters()
    {
        $media = m::mock(MediaInterface::class);
        $media->shouldReceive('getId')->andReturn(1);

        $bag = new MediaParameterBag();
        $this->assertEquals(['id' => 1], $bag->toArray($media));
        $bag->setExtra(['foo' => 'bar']);
        $this->assertEquals(['id' => 1, 'foo' => 'bar'], $bag->toArray($media));
        $this->assertFalse($bag->hasExtra('bar'));
        $bag->addExtra('bar', 'baz');
        $this->assertTrue($bag->hasExtra('bar'));
        $this->assertEquals(['id' => 1, 'foo' => 'bar', 'bar' => 'baz'], $bag->toArray($media));
        $bag->removeExtra('bar');
        $this->assertFalse($bag->hasExtra('bar'));
        $this->assertEquals(['id' => 1, 'foo' => 'bar'], $bag->toArray($media));
    }
}

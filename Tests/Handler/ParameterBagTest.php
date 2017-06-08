<?php

namespace MediaMonks\SonataMediaBundle\Tests\Handler;

use MediaMonks\SonataMediaBundle\Handler\ParameterBag;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Mockery as m;

class ParameterBagTest extends \PHPUnit_Framework_TestCase
{
    public function testGettersSetters()
    {
        $parameterBag = new ParameterBag(400, 300, ['foo' => 'bar']);
        $this->assertEquals(400, $parameterBag->getWidth());
        $this->assertEquals(300, $parameterBag->getHeight());
        $this->assertEquals(['foo' => 'bar'], $parameterBag->getExtra());

        $parameterBag->setWidth(500);
        $this->assertEquals(500, $parameterBag->getWidth());
        $this->assertEquals(300, $parameterBag->getHeight());

        $parameterBag->setHeight(400);
        $this->assertEquals(500, $parameterBag->getWidth());
        $this->assertEquals(400, $parameterBag->getHeight());

        $media = m::mock(MediaInterface::class);
        $media->shouldReceive('getId')->once()->andReturn(1);

        $this->assertEquals([
            'id' => 1,
            'width' => 500,
            'height' => 400,
            'foo' => 'bar'
        ], $parameterBag->toArray($media));
    }
}

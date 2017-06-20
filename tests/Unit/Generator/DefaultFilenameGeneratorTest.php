<?php

namespace MediaMonks\SonataMediaBundle\Tests\Unit\Generator;

use MediaMonks\SonataMediaBundle\Generator\DefaultFilenameGenerator;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\Tests\Unit\MockeryTrait;
use Mockery as m;

class DefaultFilenameGeneratorTest extends \PHPUnit_Framework_TestCase
{
    use MockeryTrait;

    public function test_generate()
    {
        $generator = new DefaultFilenameGenerator();

        $media = m::mock(MediaInterface::class);
        $media->shouldReceive('getId')->andReturn(1);
        $media->shouldReceive('getImage')->andReturn('test.jpg');
        $media->shouldReceive('getFocalPoint')->andReturn('50-50');

        $parameterBag = new ImageParameterBag(400, 300);

        $this->assertEquals('test/id_1-width_400-height_300.jpg', $generator->generate($media, $parameterBag));

        $parameterBag->addExtra('fm', 'png');

        $this->assertEquals(
            'test/fm_png-id_1-width_400-height_300.png',
            $generator->generate($media, $parameterBag)
        );
    }
}

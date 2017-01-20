<?php

namespace MediaMonks\SonataMediaBundle\Tests\Generator;

use MediaMonks\SonataMediaBundle\Generator\DefaultFilenameGenerator;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Mockery as m;

class DefaultFilenameGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function test_generate()
    {
        $generator = new DefaultFilenameGenerator();

        $media = m::mock(MediaInterface::class);
        $media->shouldReceive('getImage')->andReturn('test.jpg');

        $this->assertEquals('test/w_400-h_300.jpg', $generator->generate($media, ['w' => 400, 'h' => 300]));
        $this->assertEquals(
            'test/w_400-h_300-fm_png.png',
            $generator->generate($media, ['w' => 400, 'h' => 300, 'fm' => 'png'])
        );
    }
}

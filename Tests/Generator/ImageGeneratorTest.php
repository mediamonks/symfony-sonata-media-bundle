<?php

namespace MediaMonks\SonataMediaBundle\Tests\Generator;

use League\Flysystem\Filesystem;
use League\Glide\Api\ApiInterface;
use League\Glide\Server;
use MediaMonks\SonataMediaBundle\Generator\FilenameGeneratorInterface;
use MediaMonks\SonataMediaBundle\Generator\ImageGenerator;
use MediaMonks\SonataMediaBundle\Handler\ParameterBag;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Mockery as m;

class ImageGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function test_generate()
    {
        $filesystem = m::mock(Filesystem::class);
        $filesystem->shouldReceive('has')->once()->andReturn(true);
        $filesystem->shouldReceive('read')->once()->andReturn('foo');
        $filesystem->shouldReceive('write')->withArgs(['image_handled.jpg', 'bar'])->once()->andReturn(true);

        $api = m::mock(ApiInterface::class);
        $api->shouldReceive('run')->once()->andReturn('bar');

        $server = m::mock(Server::class);
        $server->shouldReceive('getSource')->twice()->andReturn($filesystem);
        $server->shouldReceive('getCache')->once()->andReturn($filesystem);
        $server->shouldReceive('getApi')->once()->andReturn($api);

        $filenameGenerator = m::mock(FilenameGeneratorInterface::class);
        $filenameGenerator->shouldReceive('generate')->once()->andReturn('image_handled.jpg');

        $media = m::mock(MediaInterface::class);
        $media->shouldReceive('getImage')->once()->andReturn('image.jpg');

        $parameters = new ParameterBag(400, 300);
        $imageGenerator = new ImageGenerator($server, $filenameGenerator);
        $filename = $imageGenerator->generate($media, $parameters);

        $this->assertEquals('image_handled.jpg', $filename);
    }
}

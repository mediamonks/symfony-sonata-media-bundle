<?php

namespace MediaMonks\SonataMediaBundle\Tests\Generator;

use League\Flysystem\Filesystem;
use League\Glide\Api\ApiInterface;
use League\Glide\Filesystem\FilesystemException;
use League\Glide\Server;
use MediaMonks\SonataMediaBundle\Generator\FilenameGeneratorInterface;
use MediaMonks\SonataMediaBundle\Generator\ImageGenerator;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
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
        $media->shouldReceive('getFocalPoint')->once()->andReturn('50-50');

        $parameters = new ImageParameterBag(400, 300);
        $imageGenerator = new ImageGenerator($server, $filenameGenerator);
        $filename = $imageGenerator->generate($media, $parameters);

        $this->assertEquals('image_handled.jpg', $filename);
    }

    public function testGetters()
    {
        $server = m::mock(Server::class);
        $filenameGenerator = m::mock(FilenameGeneratorInterface::class);
        $imageGenerator = new ImageGenerator(
            $server,
            $filenameGenerator,
            ['foo' => 'bar'],
            'fallback',
            'tmpPath',
            'tmpPrefix'
        );
        $this->assertEquals($server, $imageGenerator->getServer());
        $this->assertEquals($filenameGenerator, $imageGenerator->getFilenameGenerator());
        $this->assertEquals(['foo' => 'bar'], $imageGenerator->getDefaultImageParameters());
        $this->assertEquals('fallback', $imageGenerator->getFallbackImage());
        $this->assertEquals('tmpPath', $imageGenerator->getTmpPath());
        $this->assertEquals('tmpPrefix', $imageGenerator->getTmpPrefix());
    }

    public function testUnableToWriteTemporaryFile()
    {
        $this->setExpectedException(FilesystemException::class);

        $filesystem = m::mock(Filesystem::class);
        $filesystem->shouldReceive('has')->once()->andReturn(false);
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
        $media->shouldReceive('getFocalPoint')->once()->andReturn('50-50');

        $parameters = new ImageParameterBag(400, 300);

        $imageGenerator = new ImageGenerator($server,$filenameGenerator, [], null, '/non-existing-path');
        $imageGenerator->generate($media, $parameters);
    }
}

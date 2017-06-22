<?php

namespace MediaMonks\SonataMediaBundle\Tests\Unit\Generator;

use League\Flysystem\Filesystem;
use League\Glide\Api\ApiInterface;
use League\Glide\Filesystem\FilesystemException;
use League\Glide\Server;
use MediaMonks\SonataMediaBundle\Generator\FilenameGeneratorInterface;
use MediaMonks\SonataMediaBundle\Generator\ImageGenerator;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Mockery as m;
use org\bovigo\vfs\vfsStream;

class ImageGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerate()
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
        vfsStream::setup(__DIR__);
        vfsStream::setQuota(0);

        $this->setExpectedException(FilesystemException::class);

        $source = m::mock(Filesystem::class);
        $source->shouldReceive('has')->once()->andReturn(true);
        $source->shouldReceive('read')->once()->andReturn('foo');
        $source->shouldReceive('write')->withArgs(['image_handled.jpg', 'bar'])->once()->andReturn(false);

        $cache = m::mock(Filesystem::class);
        $cache->shouldReceive('has')->once()->andReturn(false);
        $cache->shouldReceive('put')->once()->andReturn(false)->andThrow(\Exception::class, 'Unable to write');

        $api = m::mock(ApiInterface::class);
        $api->shouldReceive('run')->once()->andReturn('bar');

        $server = m::mock(Server::class);
        $server->shouldReceive('getSource')->twice()->andReturn($source);
        $server->shouldReceive('getCache')->once()->andReturn($cache);
        $server->shouldReceive('getApi')->once()->andReturn($api);

        $filenameGenerator = m::mock(FilenameGeneratorInterface::class);
        $filenameGenerator->shouldReceive('generate')->once()->andReturn('image_handled.jpg');

        $media = m::mock(MediaInterface::class);
        $media->shouldReceive('getImage')->once()->andReturn('image.jpg');
        $media->shouldReceive('getFocalPoint')->once()->andReturn('50-50');

        $parameters = new ImageParameterBag(400, 300);
        $imageGenerator = new ImageGenerator($server, $filenameGenerator, [], null, __DIR__);
        $filename = $imageGenerator->generate($media, $parameters);
    }

    public function testUnableToWriteGeneratedImage()
    {
        $this->setExpectedException(FilesystemException::class);

        $source = m::mock(Filesystem::class);
        $source->shouldReceive('has')->once()->andReturn(true);
        $source->shouldReceive('read')->once()->andReturn('foo');
        $source->shouldReceive('write')->withArgs(['image_handled.jpg', 'bar'])->once()->andReturn(false);

        $cache = m::mock(Filesystem::class);
        $cache->shouldReceive('has')->once()->andReturn(false);
        $cache->shouldReceive('put')->once()->andReturn(false)->andThrow(\Exception::class, 'Unable to write');

        $api = m::mock(ApiInterface::class);
        $api->shouldReceive('run')->once()->andReturn('bar');

        $server = m::mock(Server::class);
        $server->shouldReceive('getSource')->twice()->andReturn($source);
        $server->shouldReceive('getCache')->once()->andReturn($cache);
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
}

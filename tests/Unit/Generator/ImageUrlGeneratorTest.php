<?php

namespace MediaMonks\SonataMediaBundle\Tests\Unit\Generator;

use MediaMonks\SonataMediaBundle\Generator\ImageUrlGenerator;
use MediaMonks\SonataMediaBundle\Handler\ParameterHandlerInterface;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use MediaMonks\SonataMediaBundle\Tests\Unit\MockeryTrait;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ImageUrlGeneratorTest extends TestCase
{
    use MockeryTrait;

    public function setUp(): void
    {
        parent::setUp();

        m::resetContainer();
    }

    public function testGenerate()
    {
        $router = m::mock(Router::class);
        $router->shouldReceive('generate')->withArgs(
            ['route_name', [], UrlGeneratorInterface::ABSOLUTE_PATH]
        )->andReturn('http://route/1/');

        $parameterHandler = m::mock(ParameterHandlerInterface::class);
        $parameterHandler->shouldReceive('getQueryString')->andReturn('querystring');
        $parameterHandler->shouldReceive('getRouteParameters')->andReturn([]);

        $generator = new ImageUrlGenerator($router, $parameterHandler, 'route_name');

        $media = m::mock(MediaInterface::class);
        $media->shouldReceive('getFocalPoint')->andReturn('50-50');

        $parameterBag = new ImageParameterBag(400, 300);

        $this->assertEquals('http://route/1/', $generator->generate($media, $parameterBag));

        $this->assertEquals('http://route/1/', $generator->generateImageUrl($media, 400, 300));
    }

    public function testGenerateWithRouteName()
    {
        $router = m::mock(Router::class);
        $router->shouldReceive('generate')->withArgs(
            ['route_name_custom', [], UrlGeneratorInterface::ABSOLUTE_PATH]
        )->andReturn('http://route-foo/1/');

        $parameterHandler = m::mock(ParameterHandlerInterface::class);
        $parameterHandler->shouldReceive('getQueryString')->andReturn('querystring');
        $parameterHandler->shouldReceive('getRouteParameters')->andReturn([]);

        $generator = new ImageUrlGenerator($router, $parameterHandler, 'route_name');

        $parameterBag = new ImageParameterBag(400, 300);

        $media = m::mock(MediaInterface::class);
        $media->shouldReceive('getFocalPoint')->andReturn('50-50');

        $this->assertEquals('http://route-foo/1/', $generator->generate($media, $parameterBag, 'route_name_custom'));

        $this->assertEquals('http://route-foo/1/', $generator->generateImageUrl($media, 400, 300, [], 'route_name_custom'));
    }
}

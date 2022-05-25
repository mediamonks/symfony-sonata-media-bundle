<?php

namespace MediaMonks\SonataMediaBundle\Tests\Unit\Generator;

use MediaMonks\SonataMediaBundle\Generator\AbstractUrlGenerator;
use MediaMonks\SonataMediaBundle\Generator\MediaUrlGenerator;
use MediaMonks\SonataMediaBundle\Handler\ParameterHandlerInterface;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use MediaMonks\SonataMediaBundle\Tests\Unit\MockeryTrait;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MediaUrlGeneratorTest extends TestCase
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

        $generator = new MediaUrlGenerator($router, $parameterHandler, [
            AbstractUrlGenerator::ROUTE_STREAM => 'route_name'
        ]);

        $media = m::mock(MediaInterface::class);
        $media->shouldReceive('getFocalPoint')->andReturn('50-50');

        $this->assertEquals('http://route/1/', $generator->generateStreamUrl($media));
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

        $generator = new MediaUrlGenerator($router, $parameterHandler, [
            AbstractUrlGenerator::ROUTE_STREAM => 'route_name'
        ]);

        $parameterBag = new ImageParameterBag(400, 300);

        $media = m::mock(MediaInterface::class);
        $media->shouldReceive('getFocalPoint')->andReturn('50-50');

        $this->assertEquals('http://route-foo/1/', $generator->generate($media, $parameterBag, 'route_name_custom'));
    }
}

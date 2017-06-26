<?php

namespace MediaMonks\SonataMediaBundle\Tests\Unit\Generator;

use MediaMonks\SonataMediaBundle\Generator\DownloadUrlGenerator;
use MediaMonks\SonataMediaBundle\ParameterBag\DownloadParameterBag;
use MediaMonks\SonataMediaBundle\Handler\ParameterHandlerInterface;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\Tests\Unit\MockeryTrait;
use Mockery as m;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DownloadUrlGeneratorTest extends \PHPUnit_Framework_TestCase
{
    use MockeryTrait;

    public function setUp()
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

        $generator = new DownloadUrlGenerator($router, $parameterHandler, 'route_name');

        $media = m::mock(MediaInterface::class);

        $parameterBag = new DownloadParameterBag();

        $this->assertEquals('http://route/1/', $generator->generate($media, $parameterBag));

        $this->assertEquals('http://route/1/', $generator->generateDownloadUrl($media));
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

        $generator = new DownloadUrlGenerator($router, $parameterHandler, 'route_name');

        $parameterBag = new DownloadParameterBag();

        $media = m::mock(MediaInterface::class);

        $this->assertEquals('http://route-foo/1/', $generator->generate($media, $parameterBag, 'route_name_custom'));

        $this->assertEquals('http://route-foo/1/', $generator->generateDownloadUrl($media, [],'route_name_custom'));
    }
}

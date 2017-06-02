<?php

namespace MediaMonks\SonataMediaBundle\Tests\Generator;

use MediaMonks\SonataMediaBundle\Generator\UrlGenerator;
use MediaMonks\SonataMediaBundle\Handler\ParameterBag;
use MediaMonks\SonataMediaBundle\Handler\ParameterHandlerInterface;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\Tests\MockeryTrait;
use Mockery as m;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UrlGeneratorTest extends \PHPUnit_Framework_TestCase
{
    use MockeryTrait;

    public function test_generate()
    {
        m::resetContainer();
        $router = m::mock(Router::class);
        $router->shouldReceive('generate')->withArgs(
            ['route_name', [], UrlGeneratorInterface::ABSOLUTE_PATH]
        )->andReturn('http://route/1/');

        $parameterHandler = m::mock(ParameterHandlerInterface::class);
        $parameterHandler->shouldReceive('getQueryString')->andReturn('querystring');
        $parameterHandler->shouldReceive('getRouteParameters')->andReturn([]);

        $generator = new UrlGenerator($router, $parameterHandler, 'route_name');

        $media = m::mock(MediaInterface::class);
        $media->shouldReceive('getFocalPoint')->andReturn('50-50');

        $this->assertEquals('http://route/1/', $generator->generate($media, 400,300));
    }
}

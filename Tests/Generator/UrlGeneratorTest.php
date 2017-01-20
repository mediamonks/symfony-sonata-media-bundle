<?php

namespace MediaMonks\SonataMediaBundle\Tests\Generator;

use MediaMonks\SonataMediaBundle\Generator\UrlGenerator;
use MediaMonks\SonataMediaBundle\Handler\ParameterHandlerInterface;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Mockery as m;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UrlGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function test_generate()
    {
        $router = m::mock(Router::class);
        $router->shouldReceive('generate')->withArgs(
            ['route_name', ['id' => 1], UrlGeneratorInterface::ABSOLUTE_PATH]
        )->andReturn('http://route/1/');

        $parameterHandler = m::mock(ParameterHandlerInterface::class);
        $parameterHandler->shouldReceive('getQueryString')->andReturn('querystring');

        $generator = new UrlGenerator($router, $parameterHandler, 'route_name');

        $media = m::mock(MediaInterface::class);
        $media->shouldReceive('getId')->once()->andReturn(1);

        $this->assertEquals('http://route/1/?querystring', $generator->generate($media, ['w' => 400, 'h' => 300]));
    }
}

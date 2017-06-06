<?php

namespace MediaMonks\SonataMediaBundle\Tests\Handler;

use MediaMonks\SonataMediaBundle\Exception\InvalidQueryParameterException;
use MediaMonks\SonataMediaBundle\Exception\SignatureInvalidException;
use MediaMonks\SonataMediaBundle\Handler\ParameterBag;
use MediaMonks\SonataMediaBundle\Handler\SignatureParameterHandler;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\Tests\MockeryTrait;
use Mockery as m;

class SignatureParameterHandlerTest extends \PHPUnit_Framework_TestCase
{
    use MockeryTrait;

    const ID = 1;
    const WIDTH = 400;
    const HEIGHT = 300;
    const SIGNATURE = 'f725b76a0982ac2a1c6d101f34a3917afe5b52d18fecb6b4abac82ee33b00bbc';

    /**
     * @return SignatureParameterHandler
     */
    private function getHandler()
    {
        return new SignatureParameterHandler('key', 'sha256');
    }

    /**
     * @return MediaInterface
     */
    private function getMediaMock()
    {
        $media = m::mock(MediaInterface::class);
        $media->shouldReceive('getId')->once()->andReturn(self::ID);

        return $media;
    }

    public function testGetRouteParameters()
    {
        $this->assertEquals(
            [
                'id'     => self::ID,
                'width'  => self::WIDTH,
                'height' => self::HEIGHT,
                's'      => self::SIGNATURE,
            ],
            $this->getHandler()->getRouteParameters($this->getMediaMock(), new ParameterBag(self::WIDTH, self::HEIGHT))
        );
    }

    public function testGetPayload()
    {
        $this->assertEquals(
            $this->getHandler()->getPayload($this->getMediaMock(), self::WIDTH,self::HEIGHT, [
                SignatureParameterHandler::PARAMETER_SIGNATURE => self::SIGNATURE
            ]),
            new ParameterBag(self::WIDTH, self::HEIGHT)
        );
    }

    public function testGetPayloadWithExtra()
    {
        $this->assertEquals(
            $this->getHandler()->getPayload($this->getMediaMock(), self::WIDTH,self::HEIGHT, [
                SignatureParameterHandler::PARAMETER_SIGNATURE => 'b11d65fb09d95ee462ea945943708d69b794eb71cf08090bff858cbe5fe9c6a3',
                'foo' => 'bar'
            ]),
            new ParameterBag(self::WIDTH, self::HEIGHT, ['foo' => 'bar'])
        );
    }

    public function testGetPayloadWithoutSignature()
    {
        $this->setExpectedException(SignatureInvalidException::class);

        $media = m::mock(MediaInterface::class);
        $this->getHandler()->getPayload($media, self::WIDTH, self::HEIGHT);
    }

    public function testGetPayloadWithInvalidSignature()
    {
        $this->setExpectedException(SignatureInvalidException::class);

        $this->getHandler()->getPayload($this->getMediaMock(), self::WIDTH, self::HEIGHT, [
            SignatureParameterHandler::PARAMETER_SIGNATURE => 'foobar',
        ]);
    }
}

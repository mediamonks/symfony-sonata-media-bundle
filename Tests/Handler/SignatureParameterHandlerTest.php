<?php

namespace MediaMonks\SonataMediaBundle\Tests\Handler;

use MediaMonks\SonataMediaBundle\Exception\InvalidQueryParameterException;
use MediaMonks\SonataMediaBundle\Exception\SignatureInvalidException;
use MediaMonks\SonataMediaBundle\Handler\ImageParameterBag;
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
        $media->shouldReceive('getId')->andReturn(self::ID);
        $media->shouldReceive('getFocalPoint')->andReturn('50-50');

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
            $this->getHandler()->getRouteParameters(
                $this->getMediaMock(),
                new ImageParameterBag(self::WIDTH, self::HEIGHT)
            )
        );
    }

    public function testGetPayload()
    {
        $parameterBag = new ImageParameterBag(self::WIDTH, self::HEIGHT, [
            SignatureParameterHandler::PARAMETER_SIGNATURE => self::SIGNATURE,
            'id' => self::ID
        ]);

        $this->assertEquals($this->getHandler()->verifyParameterBag($this->getMediaMock(), $parameterBag), $parameterBag);
    }

    public function testGetPayloadWithExtra()
    {
        $parameterBag = new ImageParameterBag(self::WIDTH, self::HEIGHT, [
            'id' => self::ID,
            SignatureParameterHandler::PARAMETER_SIGNATURE => 'b11d65fb09d95ee462ea945943708d69b794eb71cf08090bff858cbe5fe9c6a3',
            'foo' => 'bar'
        ]);

        $this->assertEquals($this->getHandler()->verifyParameterBag($this->getMediaMock(), $parameterBag), $parameterBag);
    }

    public function testGetPayloadWithoutSignature()
    {
        $this->setExpectedException(SignatureInvalidException::class);

        $media = $this->getMediaMock();
        $parameterBag = new ImageParameterBag(self::WIDTH, self::HEIGHT);
        $this->getHandler()->verifyParameterBag($media, $parameterBag);
    }

    public function testGetPayloadWithInvalidSignature()
    {
        $this->setExpectedException(SignatureInvalidException::class);

        $parameterBag = new ImageParameterBag(self::WIDTH, self::HEIGHT);
        $parameterBag->addExtra(SignatureParameterHandler::PARAMETER_SIGNATURE, 'foobar');

        $this->getHandler()->verifyParameterBag($this->getMediaMock(), $parameterBag);
    }
}

<?php

namespace MediaMonks\SonataMediaBundle\Tests\Handler;

use MediaMonks\SonataMediaBundle\Exception\InvalidQueryParameterException;
use MediaMonks\SonataMediaBundle\Handler\SignatureParameterHandler;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Mockery as m;
use Symfony\Component\HttpFoundation\Request;

class SignatureParameterHandlerTest extends \PHPUnit_Framework_TestCase
{
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
        $media->shouldReceive('getId')->once()->andReturn(1);

        return $media;
    }

    public function test_getQueryString()
    {
        $media = $this->getMediaMock();
        $handler = $this->getHandler();

        $this->assertEquals(
            'w=400&h=300&s=51bf85519cc41a9b9787005a1370b01959e0f82644de1331cae3302efc57910a',
            $handler->getQueryString($media, ['w' => 400, 'h' => 300])
        );
    }

    public function test_getPayload()
    {
        $request = new Request([
            'w' => 400,
            'h' => 300,
            's' => '51bf85519cc41a9b9787005a1370b01959e0f82644de1331cae3302efc57910a'
        ]);

        $media = $this->getMediaMock();
        $handler = $this->getHandler();

        $this->assertEquals(['w' => 400, 'h' => 300], $handler->getPayload($media, $request));
    }

    public function test_getPayloadWithInvalidSignature()
    {
        $this->setExpectedException(InvalidQueryParameterException::class);

        $request = new Request([
            'w' => 400,
            'h' => 300,
            's' => '41bf85519cc41a9b9787005a1370b01959e0f82644de1331cae3302efc57910a'
        ]);

        $media = $this->getMediaMock();
        $handler = $this->getHandler();

        $this->assertEquals(['w' => 400, 'h' => 300], $handler->getPayload($media, $request));
    }
}

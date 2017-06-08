<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

use MediaMonks\SonataMediaBundle\Handler\ParameterBag;
use MediaMonks\SonataMediaBundle\Handler\SignatureParameterHandler;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Mockery as m;
use Symfony\Component\DomCrawler\Form;

abstract class AbstractProviderTestAbstract extends AbstractBaseFunctionTest
{
    const BASE_PATH = '/mediamonks/sonatamedia/media/';

    /**
     * @var Client
     */
    protected $client;

    protected function setUp()
    {
        parent::setUp();

        $this->client = $this->getAuthenticatedClient();
        $this->client->followRedirects();

        $this->loadFixtures([]);
    }

    protected function verifyMediaImageIsGenerated()
    {
        $media = m::mock(MediaInterface::class);
        $media->shouldReceive('getId')->andReturn(1);

        $parameterBag = new ParameterBag(400, 300);

        $signature = new SignatureParameterHandler(self::$kernel->getContainer()->getParameter('secret'));
        $parameters = $signature->getRouteParameters($media, $parameterBag);

        $this->client->request(
            'GET',
            sprintf(
                '%s%d/image/%d/%d?s=%s',
                self::BASE_PATH,
                $media->getId(),
                $parameterBag->getWidth(),
                $parameterBag->getHeight(),
                $parameters['s']
            )
        );

        $this->assertNumberOfFilesInPath(1, $this->getMediaPathPublic());
    }
}

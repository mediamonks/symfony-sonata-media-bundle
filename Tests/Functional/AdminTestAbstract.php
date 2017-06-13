<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

use MediaMonks\SonataMediaBundle\Handler\ImageParameterBag;
use MediaMonks\SonataMediaBundle\Handler\SignatureParameterHandler;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Mockery as m;
use Symfony\Component\DomCrawler\Form;

abstract class AdminTestAbstract extends AbstractBaseFunctionTest
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
        $media->shouldReceive('getFocalPoint')->andReturn('50-50');

        $parameterBag = new ImageParameterBag(400, 300);

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

    protected function uploadImage()
    {
        $provider = 'image';
        $crawler = $this->client->request('GET', self::BASE_PATH.'create?provider='.$provider);
        $form = $crawler->selectButton('Create')->form();
        $this->assertSonataFormValues(
            $form,
            [
                'provider' => $provider,
            ]
        );
        $this->setFormBinaryContent($form, $this->getFixturesPath().'monk.jpg');
        return $this->client->submit($form);
    }
}

<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
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
        $this->emptyFolder($this->getMediaPathPublic());

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

//        var_dump($this->client->getResponse()->getContent());die;

        $this->assertNumberOfFilesInPath(1, $this->getMediaPathPublic());

        $parameterBag = new ImageParameterBag(800, 600);
        $signature = new SignatureParameterHandler(self::$kernel->getContainer()->getParameter('secret'));
        $parameters = $signature->getRouteParameters($media, $parameterBag);

        $this->client->followRedirects(false);
        $this->client->request(
            'GET',
            sprintf(
                '/media/image/%d/%d/%d?s=%s',
                $media->getId(),
                $parameterBag->getWidth(),
                $parameterBag->getHeight(),
                $parameters['s']
            )
        );

        $this->assertNumberOfFilesInPath(2, $this->getMediaPathPublic());
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

<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

use MediaMonks\SonataMediaBundle\Handler\SignatureParameterHandler;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use Mockery as m;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

abstract class AdminTestAbstract extends AbstractBaseFunctionTest
{
    const BASE_PATH = '/mediamonks/sonatamedia/media/';

    protected KernelBrowser $browser;

    protected function setUp(): void
    {
        $this->browser = $this->getAuthenticatedClient();
        $this->browser->followRedirects();

        parent::setUp();

        $this->loadFixtures();
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

        $this->browser->followRedirects();
        $this->browser->request(
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

        $this->assertEquals(200, $this->browser->getResponse()->getStatusCode());
        $this->assertNumberOfFilesInPath(1, $this->getMediaPathPublic());

        $parameterBag = new ImageParameterBag(800, 600);
        $signature = new SignatureParameterHandler(self::$kernel->getContainer()->getParameter('secret'));
        $parameters = $signature->getRouteParameters($media, $parameterBag);

        $this->browser->followRedirects(false);
        $this->browser->request(
            'GET',
            sprintf(
                '/media/image/redirect/%d/%d/%d?s=%s',
                $media->getId(),
                $parameterBag->getWidth(),
                $parameterBag->getHeight(),
                $parameters['s']
            )
        );

        $this->assertEquals(302, $this->browser->getResponse()->getStatusCode());
        $this->assertNumberOfFilesInPath(2, $this->getMediaPathPublic());
    }

    protected function uploadImage()
    {
        $provider = 'image';
        $crawler = $this->browser->request('GET', self::BASE_PATH . 'create?provider=' . $provider);
        $form = $crawler->selectButton('Create')->form();
        $this->assertSonataFormValues(
            $form,
            [
                'provider' => $provider,
            ]
        );
        $this->setFormBinaryContent($form, $this->getFixturesPath() . 'monk.jpg');

        return $this->browser->submit($form);
    }
}

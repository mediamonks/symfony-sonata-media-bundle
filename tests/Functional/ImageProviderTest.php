<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

class ImageProviderTest extends AdminTestAbstract
{
    public function testImage()
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

        $this->setFormBinaryContent($form, $this->getFixturesPath().'monk.jpg');

        $crawler = $this->browser->submit($form);
        $form = $crawler->selectButton('Update')->form();

        $this->assertSonataFormValues($form, ['title' => 'monk']);

        $this->browser->request('GET', self::BASE_PATH . 'list');
        $this->assertStringContainsString('monk', $this->browser->getResponse()->getContent());

        $this->assertNumberOfFilesInPath(1, $this->getMediaPathPrivate());

        $this->verifyMediaImageIsGenerated();

        $crawler = $this->browser->request('GET', '/twig');

        $this->assertEquals(
            5,
            $crawler->filter('img')->count()
        );

        $this->assertEquals(
            0,
            $crawler->filter('iframe')->count()
        );

        $this->assertEquals(
            1,
            $crawler->filter('a')->count()
        );

        $this->browser->request('GET', '/api');
        $data = json_decode($this->browser->getResponse()->getContent(), true);

        $this->assertArrayHasKey('url', $data);
    }

    public function testEmptyFileUpload()
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

        $this->browser->submit($form);

        $this->assertStringContainsString('This value should not be blank', $this->browser->getResponse()->getContent());
        $this->assertStringContainsString('This value should not be blank', $this->browser->getResponse()->getContent());
    }
}

<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

class ImageProviderTest extends AbstractProviderTestAbstract
{
    public function testImage()
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

        print_r($form->getPhpFiles());

        $this->setFormBinaryContent($form, $this->getFixturesPath().'monk.jpg');

        print_r($form->getPhpFiles());

        $form[sprintf('%s[binaryContent]', $this->getSonataFormBaseKey($form))]->upload($this->getFixturesPath().'monk.jpg');

        print_r($form->getPhpFiles());

        $crawler = $this->client->submit($form);
        $form = $crawler->selectButton('Update')->form();

        $this->assertSonataFormValues($form, ['title' => 'monk']);

        $this->client->request('GET', self::BASE_PATH.'list');
        $this->assertContains('monk', $this->client->getResponse()->getContent());

        $this->assertNumberOfFilesInPath(1, $this->getMediaPathPrivate());
    }
}

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

        $this->setFormBinaryContent($form, $this->getFixturesPath().'monk.jpg');

        try {
            $crawler = $this->client->submit($form);
            $form = $crawler->selectButton('Update')->form();
        }
        catch (\Exception $e) {
            echo $this->client->getResponse()->getContent();
            return;
        }

        $this->assertSonataFormValues($form, ['title' => 'monk']);

        $this->client->request('GET', self::BASE_PATH.'list');
        $this->assertContains('monk', $this->client->getResponse()->getContent());

        $this->assertNumberOfFilesInPath(1, $this->getMediaPathPrivate());
    }
}

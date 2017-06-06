<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

class FileProviderTest extends AbstractProviderTestAbstract
{
    public function testFile()
    {
        $provider = 'file';

        $crawler = $this->client->request('GET', self::BASE_PATH.'create?provider='.$provider);

        $form = $crawler->selectButton('Create')->form();

        $this->assertSonataFormValues(
            $form,
            [
                'provider' => $provider,
            ]
        );

        $this->setFormBinaryContent($form, $this->getFixturesPath().'text.txt');

        try {
            $crawler = $this->client->submit($form);
            $form = $crawler->selectButton('Update')->form();
        }
        catch (\Exception $e) {
            echo $this->client->getResponse()->getContent();
            return;
        }

        $this->assertSonataFormValues($form, ['title' => 'text']);

        $this->client->request('GET', self::BASE_PATH.'list');
        $this->assertContains('text', $this->client->getResponse()->getContent());

        $this->assertNumberOfFilesInPath(2, $this->getMediaPathPrivate());
    }
}

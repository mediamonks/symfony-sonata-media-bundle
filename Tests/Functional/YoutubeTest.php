<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Client;

class YoutubeTest extends BaseFunctionTest
{
    /**
     * @var Client
     */
    private $client;

    protected function setUp()
    {
        if (version_compare(PHP_VERSION, '5.6.0', '<')) {
            $this->markTestSkipped('Functional tests only run on PHP 5.6+');
        }

        parent::setUp();

        $this->client = $this->getAuthenticatedClient();
        $this->client->followRedirects();
    }

    public function youtubeAdd()
    {
        $this->loadFixtures([]);

        $crawler = $this->client->request('GET', '/mediamonks/sonatamedia/media/create?provider=youtube');

        $this->assertContains('YouTube ID or URL', $this->client->getResponse()->getContent());

        $form = $crawler->selectButton('Create')->form();

        $this->assertSonataFormValues($form, [
            'provider' => 'youtube',
        ]);

        foreach ($form->getValues() as $k => $v) {
            if (strpos($k, '[provider]') !== false) {
                $this->assertEquals('youtube', $v);
            }
            if (strpos($k, '[providerReference]') !== false) {
                $form->setValues([
                    $k => 'https://www.youtube.com/watch?v=uN2nqdRLhQ8'
                ]);
            }
        }

        $crawler = $this->client->submit($form);
        $form = $crawler->selectButton('Update')->form();
        $formValues = $this->getSonataFormValues($form);

        $this->assertEquals('youtube', $formValues['provider']);
        $this->assertEquals('uN2nqdRLhQ8', $formValues['providerReference']);
        $this->assertEquals('MediaMonks Mixtape Vol  II', $formValues['title']);
        $this->assertEquals('MediaMonks', $formValues['authorName']);

        $this->client->request('GET', '/mediamonks/sonatamedia/media/list');
        $this->assertContains('MediaMonks Mixtape Vol  II', $this->client->getResponse()->getContent());
    }

    public function testYoutubeUpdate()
    {
        $this->youtubeAdd();

        $crawler = $this->client->request('GET', '/mediamonks/sonatamedia/media/1/edit');

        $this->assertContains('YouTube ID or URL', $this->client->getResponse()->getContent());
        $this->assertContains('MediaMonks Mixtape Vol  II', $this->client->getResponse()->getContent());

        $form = $crawler->selectButton('Update')->form();
        $this->updateFormValues($form, [
            'title' => 'Updated Title',
            'description' => 'Updated Description'
        ]);
        $crawler = $this->client->submit($form);

        $formValues = $this->getSonataFormValues($form);

        $this->assertEquals('youtube', $formValues['provider']);
        $this->assertEquals('uN2nqdRLhQ8', $formValues['providerReference']);
        $this->assertEquals('Updated Title', $formValues['title']);
        $this->assertEquals('Updated Description', $formValues['description']);
        $this->assertEquals('MediaMonks', $formValues['authorName']);

        $this->client->request('GET', '/mediamonks/sonatamedia/media/list');
        $this->assertContains('Updated Title', $this->client->getResponse()->getContent());
    }
}

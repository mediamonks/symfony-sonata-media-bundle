<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

class YoutubeProviderTest extends AbstractOembedProviderTestAbstract
{
    public function testYoutube()
    {
        $this->providerFlow(
            'youtube',
            'https://www.youtube.com/watch?v=uN2nqdRLhQ8',
            [
                'providerReference' => 'uN2nqdRLhQ8',
                'title' => 'MediaMonks Mixtape Vol  II',
                'description' => '',
                'authorName' => 'MediaMonks',
                'copyright' => '',
                'focalPoint' => '50-50'
            ]
        );

        $crawler = $this->client->request('GET', '/twig');

        $this->assertEquals(
            2,
            $crawler->filter('img')->count()
        );

        $this->assertEquals(
            2,
            $crawler->filter('iframe')->count()
        );

        $this->assertEquals(
            0,
            $crawler->filter('a')->count()
        );
    }

    public function testUnexistingVideo()
    {
        $provider = 'youtube';
        $providerReference = 'foobar123123123';

        \VCR\VCR::insertCassette('youtube_unexisting');
        $crawler = $this->client->request('GET', self::BASE_PATH.'create?provider='.$provider);

        $form = $crawler->selectButton('Create')->form();

        $this->assertSonataFormValues(
            $form,
            [
                'provider' => $provider,
            ]
        );

        $this->updateSonataFormValues(
            $form,
            [
                'providerReference' => $providerReference,
            ]
        );

        $this->client->submit($form);

        $this->assertContains('does not exist', $this->client->getResponse()->getContent());

        \VCR\VCR::eject();
    }
}

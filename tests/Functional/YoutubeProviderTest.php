<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

use VCR\VCR;

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

        $crawler = $this->browser->request('GET', '/twig');

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

        VCR::insertCassette('youtube_unexisting');
        $crawler = $this->browser->request('GET', self::BASE_PATH . 'create?provider=' . $provider);

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

        $this->browser->submit($form);

        $this->assertStringContainsString('does not exist', $this->browser->getResponse()->getContent());

        VCR::eject();
    }
}

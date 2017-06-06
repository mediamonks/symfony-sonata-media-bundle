<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

class YoutubeAbstractOembedProviderTest extends AbstractOembedProviderTestAbstract
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
    }
}

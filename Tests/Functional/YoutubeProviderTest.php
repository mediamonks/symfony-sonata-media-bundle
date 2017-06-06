<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

class YoutubeProviderTest extends ProviderTest
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

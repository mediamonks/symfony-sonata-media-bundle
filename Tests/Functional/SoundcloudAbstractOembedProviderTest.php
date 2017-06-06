<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

class SoundcloudAbstractOembedProviderTest extends AbstractOembedProviderTestAbstract
{
    public function testVimeo()
    {
        $this->providerFlow(
            'soundcloud',
            'https://soundcloud.com/mediamonks-2/old-spice-workout-anthem',
            [
                'providerReference' => 'mediamonks-2/old-spice-workout-anthem',
                'title' => 'Old Spice Workout Anthem by MediaMonks',
                'authorName' => 'MediaMonks',
                'copyright' => '',
                'focalPoint' => '50-50'
            ]
        );
    }
}

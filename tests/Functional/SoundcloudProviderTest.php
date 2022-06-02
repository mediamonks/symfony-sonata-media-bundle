<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

class SoundcloudProviderTest extends AbstractOembedProviderTestAbstract
{
    public function testSoundcloud()
    {
        $this->providerFlow(
            'soundcloud',
            'https://soundcloud.com/mediamonks-music/old-spice-workout-anthem',
            [
                'providerReference' => 'mediamonks-music/old-spice-workout-anthem',
                'title' => 'Old Spice Workout Anthem by Media.Monks',
                'authorName' => 'Media.Monks',
                'copyright' => '',
                'focalPoint' => '50-50'
            ]
        );
    }
}

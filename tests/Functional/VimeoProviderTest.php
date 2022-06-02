<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

class VimeoProviderTest extends AbstractOembedProviderTestAbstract
{
    public function testVimeo()
    {
        $this->providerFlow(
            'vimeo',
            'https://vimeo.com/184376204',
            [
                'providerReference' => '184376204',
                'title' => 'MediaMonks Mixtape Vol. II',
                'authorName' => 'Media.Monks',
                'copyright' => '',
                'focalPoint' => '50-50'
            ]
        );
    }
}

<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

class VimeoAbstractOembedProviderTest extends AbstractOembedProviderTestAbstract
{
    public function testVimeo()
    {
        $this->providerFlow(
            'vimeo',
            'https://vimeo.com/184376204',
            [
                'providerReference' => '184376204',
                'title' => 'MediaMonks Mixtape Vol. II',
                'authorName' => 'MediaMonks',
                'copyright' => '',
                'focalPoint' => '50-50'
            ]
        );
    }
}

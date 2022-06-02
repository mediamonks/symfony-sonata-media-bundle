<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional\src\Mock;

use MediaMonks\SonataMediaBundle\Model\AbstractMedia;
use MediaMonks\SonataMediaBundle\Provider\YouTubeProvider;

class YouTubeProviderMock extends YouTubeProvider
{
    public function refreshImage(AbstractMedia $media): void
    {
    }
}

<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional\src\Mock;

use MediaMonks\SonataMediaBundle\Model\AbstractMedia;
use MediaMonks\SonataMediaBundle\Provider\SoundCloudProvider;

class SoundCloudProviderMock extends SoundCloudProvider
{
    public function refreshImage(AbstractMedia $media): void
    {
    }
}

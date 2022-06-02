<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional\src\Mock;

use MediaMonks\SonataMediaBundle\Model\AbstractMedia;
use MediaMonks\SonataMediaBundle\Provider\VimeoProvider;

class VimeoProviderMock extends VimeoProvider
{
    public function refreshImage(AbstractMedia $media): void
    {
    }
}

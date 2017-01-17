<?php

namespace MediaMonks\SonataMediaBundle;

use MediaMonks\SonataMediaBundle\DependencyInjection\MediaMonksSonataMediaExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Robert Slootjes <robert@mediamonks.com>
 */
class MediaMonksSonataMediaBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new MediaMonksSonataMediaExtension();
        }

        return $this->extension;
    }
}

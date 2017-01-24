<?php

namespace MediaMonks\SonataMediaBundle;

use MediaMonks\SonataMediaBundle\DependencyInjection\Compiler\ProviderPass;
use MediaMonks\SonataMediaBundle\DependencyInjection\MediaMonksSonataMediaExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Robert Slootjes <robert@mediamonks.com>
 */
class MediaMonksSonataMediaBundle extends Bundle
{
    const BUNDLE_CONFIG_NAME = 'mediamonks_sonata_media';

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ProviderPass());
    }

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

<?php

namespace MediaMonks\SonataMediaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ProviderPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds('sonata_media.provider');
        foreach ($taggedServices as $id => $tags) {
            $container->getDefinition($id)->replaceArgument(0, new Reference('mediamonks.sonata_media.filesystem.private'));
        }
    }
}

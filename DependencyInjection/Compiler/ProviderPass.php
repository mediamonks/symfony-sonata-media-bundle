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
        $config = $container->getExtensionConfig('mediamonks_sonata_media')[0];

        if (empty($config['image_constraints'])) {
            $config['image_constraints'] = [];
        }

        $taggedServices = $container->findTaggedServiceIds('sonata_media.provider');
        foreach ($taggedServices as $id => $tags) {
            $container->getDefinition($id)->addMethodCall(
                'setFilesystem',
                [new Reference('mediamonks.sonata_media.filesystem.private')]
            );
            $container->getDefinition($id)->addMethodCall(
                'setImageConstraintOptions',
                [$config['image_constraints']]
            );
        }
    }
}

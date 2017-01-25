<?php

namespace MediaMonks\SonataMediaBundle\DependencyInjection;

use MediaMonks\SonataMediaBundle\MediaMonksSonataMediaBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class MediaMonksSonataMediaExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setAlias('mediamonks.sonata_media.filesystem.private', $config['filesystem_private']);
        $container->setAlias('mediamonks.sonata_media.filesystem.public', $config['filesystem_public']);

        $container->getDefinition('mediamonks.sonata_media.glide.server')
            ->replaceArgument(
                0,
                array_merge(
                    $config['glide'],
                    [
                        'source' => new Reference($config['filesystem_private']),
                        'cache'  => new Reference($config['filesystem_public']),
                    ]
                )
            );

        $container->getDefinition('mediamonks.sonata_media.provider.file')
            ->replaceArgument(0, $config['file_constraints']);

        $providerPool = $container->getDefinition('mediamonks.sonata_media.provider.pool');
        foreach ($config['providers'] as $provider) {
            $providerPool->addMethodCall('addProvider', [new Reference($provider)]);
        }

        $container->getDefinition('mediamonks.sonata_media.utility.image')
            ->replaceArgument(2, $config['redirect_url'])
            ->replaceArgument(3, $config['redirect_cache_ttl'])
            ->replaceArgument(4, $config['default_image_parameters']);

        $container->getDefinition('mediamonks.sonata_media.utility.download')
            ->replaceArgument(0, new Reference($config['filesystem_private']));
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return MediaMonksSonataMediaBundle::BUNDLE_CONFIG_NAME;
    }
}

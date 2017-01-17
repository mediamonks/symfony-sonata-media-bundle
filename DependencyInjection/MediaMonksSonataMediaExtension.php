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

        $container->getDefinition('mediamonks.media.provider.image')
            ->replaceArgument(0, new Reference($config['filesystem_source']));

        $container->getDefinition('mediamonks.media.provider.youtube')
            ->replaceArgument(0, new Reference($config['filesystem_source']));

        $container->getDefinition('mediamonks.media.glide.server')
            ->replaceArgument(
                0,
                array_merge(
                    $config['glide'],
                    [
                        'source' => new Reference($config['filesystem_source']),
                        'cache'  => new Reference($config['filesystem_source']),
                    ]
                )
            );

        $container->getDefinition('mediamonks.media.helper.controller')
            ->replaceArgument(2, $config['redirect_url'])
            ->replaceArgument(3, $config['redirect_cache_ttl']);

        $providerPool = $container->getDefinition('mediamonks.media.provider.pool');
        foreach ($config['providers'] as $provider) {
            $providerPool->addMethodCall('addProvider', [new Reference($provider)]);
        }
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return MediaMonksSonataMediaBundle::BUNDLE_CONFIG_NAME;
    }
}

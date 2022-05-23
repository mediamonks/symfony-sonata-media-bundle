<?php

namespace MediaMonks\SonataMediaBundle\DependencyInjection;

use Exception;
use MediaMonks\SonataMediaBundle\Admin\MediaAdmin;
use MediaMonks\SonataMediaBundle\Generator\ImageGenerator;
use MediaMonks\SonataMediaBundle\MediaMonksSonataMediaBundle;
use MediaMonks\SonataMediaBundle\Provider\FileProvider;
use MediaMonks\SonataMediaBundle\Provider\ProviderPool;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class MediaMonksSonataMediaExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $container->setParameter('mediamonks.sonata_media.config', $config);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        if (empty($config['filesystem_private']) || empty($config['filesystem_public'])) {
            throw new Exception('Both a private and a public filesystem must be set');
        }

        $container->setAlias('mediamonks.sonata_media.filesystem.private', $config['filesystem_private']);
        $container->setAlias('mediamonks.sonata_media.filesystem.public', $config['filesystem_public']);

        $container->getDefinition(MediaAdmin::class)
                  ->setClass($config['admin_class'])
                  ->setArgument(0, null)
                  ->setArgument(1, $config['model_class'])
                  ->setArgument(2, $config['controller_class']);

        $container->setParameter('mediamonks.sonata_media.entity.class', $config['model_class']);

        $container->getDefinition(FileProvider::class)
                  ->setArgument(0, $config['file_constraints']);

        $providerPool = $container->getDefinition(ProviderPool::class);
        foreach ($config['providers'] as $provider) {
            $providerPool->addMethodCall('addProvider', [new Reference($provider)]);
        }

        $container->setParameter('mediamonks.sonata_media.base_url', $config['redirect_url']);
        $container->setParameter('mediamonks.sonata_media.cache_ttl', $config['redirect_cache_ttl']);

        $container->getDefinition(ImageGenerator::class)
                  ->setArgument(2, $config['default_image_parameters'])
                  ->setArgument(3, $config['fallback_image'])
                  ->setArgument(4, $config['tmp_path'])
                  ->setArgument(5, $config['tmp_prefix']);

        $formResource = $config['templates']['form'];
        $twigFormResourceParameterId = 'twig.form.resources';
        if ($container->hasParameter($twigFormResourceParameterId)) {
            $twigFormResources = $container->getParameter($twigFormResourceParameterId);
            if (!empty($formResource) && !in_array($formResource, $twigFormResources)) {
                $twigFormResources[] = $formResource;
            }

            $container->setParameter($twigFormResourceParameterId, $twigFormResources);
        }

        $container->setParameter('mediamonks.sonata_media.default_route.image', $config['route_image']);
        $container->setParameter('mediamonks.sonata_media.default_route.download', $config['route_download']);
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return MediaMonksSonataMediaBundle::BUNDLE_CONFIG_NAME;
    }
}

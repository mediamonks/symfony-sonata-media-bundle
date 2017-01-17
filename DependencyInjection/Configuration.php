<?php

namespace MediaMonks\SonataMediaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mediamonks_sonata_media');

        $this->addFilesystem($rootNode);
        $this->addMediaBaseUrl($rootNode);
        $this->addDefaultMediaProvider($rootNode);
        $this->addCacheTtl($rootNode);
        $this->addProviders($rootNode);
        $this->addGlideConfig($rootNode);

        // @todo routes?

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addFilesystem(ArrayNodeDefinition $node)
    {
        $node->children()
            ->scalarNode('filesystem_source')
            ->end();

        $node->children()
            ->scalarNode('filesystem_cache')
            ->defaultNull()
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addMediaBaseUrl(ArrayNodeDefinition $node)
    {
        $node->children()
            ->scalarNode('media_base_url')
            ->defaultNull()
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addDefaultMediaProvider(ArrayNodeDefinition $node)
    {
        $node->children()
            ->scalarNode('default_media_provider')
            ->defaultValue('mediamonks.media.provider.image')
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addCacheTtl(ArrayNodeDefinition $node)
    {
        $node->children()
            ->scalarNode('redirect_cache_ttl')
            ->defaultValue(60 * 60 * 24 * 90) // 90 days
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addProviders(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('providers')
            ->defaultValue([
                'mediamonks.media.provider.image',
                'mediamonks.media.provider.youtube'
            ])
            ->prototype('scalar')->end()
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addGlideConfig(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('glide')
            ->prototype('scalar')->end()
            ->end();
    }
}

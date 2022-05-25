<?php

namespace MediaMonks\SonataMediaBundle\DependencyInjection;

use MediaMonks\SonataMediaBundle\Admin\MediaAdmin;
use MediaMonks\SonataMediaBundle\Controller\CRUDController;
use MediaMonks\SonataMediaBundle\MediaMonksSonataMediaBundle;
use MediaMonks\SonataMediaBundle\Provider\FileProvider;
use MediaMonks\SonataMediaBundle\Provider\ImageProvider;
use MediaMonks\SonataMediaBundle\Provider\SoundCloudProvider;
use MediaMonks\SonataMediaBundle\Provider\VimeoProvider;
use MediaMonks\SonataMediaBundle\Provider\YouTubeProvider;
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
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(MediaMonksSonataMediaBundle::BUNDLE_CONFIG_NAME);
        $rootNode = $treeBuilder->getRootNode();

        $this->addFilesystem($rootNode);
        $this->addRedirectUrl($rootNode);
        $this->addRedirectCacheTtl($rootNode);
        $this->addProviders($rootNode);
        $this->addGlideConfig($rootNode);
        $this->addDefaultImageParameters($rootNode);
        $this->addImageConstraints($rootNode);
        $this->addFileConstraints($rootNode);
        $this->addFallbackImage($rootNode);
        $this->addTmp($rootNode);
        $this->addTemplates($rootNode);
        $this->addRoutes($rootNode);
        $this->addClasses($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addFilesystem(ArrayNodeDefinition $node): void
    {
        $node->children()
             ->scalarNode('filesystem_private')
             ->end();

        $node->children()
             ->scalarNode('filesystem_public')
             ->defaultNull()
             ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addRedirectUrl(ArrayNodeDefinition $node): void
    {
        $node->children()
             ->scalarNode('redirect_url')
             ->defaultNull()
             ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addRedirectCacheTtl(ArrayNodeDefinition $node): void
    {
        $node->children()
             ->scalarNode('redirect_cache_ttl')
             ->defaultValue(60 * 60 * 24 * 90)// 90 days
             ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addProviders(ArrayNodeDefinition $node): void
    {
        $node->children()
             ->arrayNode('providers')
             ->defaultValue(
                 [
                     FileProvider::class,
                     ImageProvider::class,
                     YouTubeProvider::class,
                     VimeoProvider::class,
                     SoundCloudProvider::class
                 ]
             )
             ->prototype('scalar')->end()
             ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addGlideConfig(ArrayNodeDefinition $node): void
    {
        $node->children()
             ->arrayNode('glide')
             ->prototype('scalar')->end()
             ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addDefaultImageParameters(ArrayNodeDefinition $node): void
    {
        $node->children()
             ->arrayNode('default_image_parameters')
             ->prototype('scalar')->end()
             ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addImageConstraints(ArrayNodeDefinition $node): void
    {
        $node->children()
             ->variableNode('image_constraints')
             ->defaultValue([
                 'minWidth' => 100,
                 'minHeight' => 100,
                 'maxWidth' => 3000,
                 'maxHeight' => 3000
             ])
             ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addFileConstraints(ArrayNodeDefinition $node): void
    {
        $node->children()
             ->variableNode('file_constraints')
             ->defaultValue([
                 'maxSize' => '5M',
                 'extensions' => ['pdf', 'csv', 'txt', 'docx']
             ])
             ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addFallbackImage(ArrayNodeDefinition $node): void
    {
        $node->children()
             ->scalarNode('fallback_image')
             ->defaultNull()
             ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addTmp(ArrayNodeDefinition $node): void
    {
        $node->children()
             ->scalarNode('tmp_path')
             ->defaultNull()
             ->end();

        $node->children()
             ->scalarNode('tmp_prefix')
             ->defaultNull()
             ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addTemplates(ArrayNodeDefinition $node): void
    {
        $node->children()
             ->variableNode('templates')
             ->defaultValue([
                 'form' => '@MediaMonksSonataMedia/Form/form.html.twig'
             ])
             ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addRoutes(ArrayNodeDefinition $node): void
    {
        $node->children()
             ->scalarNode('route_image_stream')
             ->defaultValue('mediamonks_media_image_stream')
             ->end();
        $node->children()
             ->scalarNode('route_image_download')
             ->defaultValue('mediamonks_media_image_download')
             ->end();
        $node->children()
             ->scalarNode('route_image_redirect')
             ->defaultValue('mediamonks_media_image_redirect')
             ->end();

        $node->children()
             ->scalarNode('route_stream')
             ->defaultValue('mediamonks_media_stream')
             ->end();
        $node->children()
             ->scalarNode('route_download')
             ->defaultValue('mediamonks_media_download')
             ->end();
        $node->children()
             ->scalarNode('route_redirect')
             ->defaultValue('mediamonks_media_redirect')
             ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addClasses(ArrayNodeDefinition $node): void
    {
        $node->children()
             ->scalarNode('model_class')
             ->defaultValue('App\Entity\Media')
             ->end();

        $node->children()
             ->scalarNode('admin_class')
             ->defaultValue(MediaAdmin::class)
             ->end();

        $node->children()
             ->scalarNode('controller_class')
             ->defaultValue(CRUDController::class)
             ->end();
    }
}

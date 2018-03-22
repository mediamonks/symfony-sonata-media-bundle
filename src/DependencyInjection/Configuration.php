<?php

namespace MediaMonks\SonataMediaBundle\DependencyInjection;

use MediaMonks\SonataMediaBundle\Admin\MediaAdmin;
use MediaMonks\SonataMediaBundle\Controller\CRUDController;
use MediaMonks\SonataMediaBundle\Entity\Media;
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
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(MediaMonksSonataMediaBundle::BUNDLE_CONFIG_NAME);

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
    private function addFilesystem(ArrayNodeDefinition $node)
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
    private function addRedirectUrl(ArrayNodeDefinition $node)
    {
        $node->children()
            ->scalarNode('redirect_url')
            ->defaultNull()
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addRedirectCacheTtl(ArrayNodeDefinition $node)
    {
        $node->children()
            ->scalarNode('redirect_cache_ttl')
            ->defaultValue(60 * 60 * 24 * 90)// 90 days
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addProviders(ArrayNodeDefinition $node)
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
    private function addGlideConfig(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('glide')
            ->prototype('scalar')->end()
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addDefaultImageParameters(ArrayNodeDefinition $node)
    {
        $node->children()
            ->arrayNode('default_image_parameters')
            ->prototype('scalar')->end()
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addImageConstraints(ArrayNodeDefinition $node)
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
    private function addFileConstraints(ArrayNodeDefinition $node)
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
    private function addFallbackImage(ArrayNodeDefinition $node)
    {
        $node->children()
            ->scalarNode('fallback_image')
            ->defaultNull()
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addTmp(ArrayNodeDefinition $node)
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
    private function addTemplates(ArrayNodeDefinition $node)
    {
        $node->children()
            ->variableNode('templates')
            ->defaultValue([
                'form' => 'MediaMonksSonataMediaBundle:Form:form.html.twig'
            ])
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addRoutes(ArrayNodeDefinition $node)
    {
        $node->children()
            ->scalarNode('route_image')
            ->defaultValue('mediamonks_media_image')
            ->end();

        $node->children()
            ->scalarNode('route_download')
            ->defaultValue('mediamonks_media_download')
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addClasses(ArrayNodeDefinition $node)
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

<?php

namespace MediaMonks\SonataMediaBundle\Twig\Extension;

use MediaMonks\SonataMediaBundle\Generator\UrlGenerator;
use MediaMonks\SonataMediaBundle\Provider\ProviderInterface;
use MediaMonks\SonataMediaBundle\Provider\ProviderPool;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;

class MediaExtension extends \Twig_Extension
{
    /**
     * @var ProviderPool
     */
    private $providerPool;

    /**
     * @var UrlGenerator
     */
    private $urlGenerator;

    /**
     * @param ProviderPool $providerPool
     * @param UrlGenerator $urlGenerator
     */
    public function __construct(ProviderPool $providerPool, UrlGenerator $urlGenerator)
    {
        $this->providerPool = $providerPool;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'media';
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter(
                'media', [$this, 'media'], [
                    'needs_environment' => true,
                    'is_safe'           => ['html'],
                ]
            ),
            new \Twig_SimpleFilter(
                'media_embed', [$this, 'mediaEmbed'], [
                    'needs_environment' => true,
                    'is_safe'           => ['html'],
                ]
            ),
            new \Twig_SimpleFilter(
                'media_image', [$this, 'mediaImage'], [
                    'needs_environment' => true,
                    'is_safe'           => ['html'],
                ]
            ),
            new \Twig_SimpleFilter(
                'media_download', [$this, 'mediaDownload'], [
                    'needs_environment' => true,
                    'is_safe'           => ['html'],
                ]
            ),
            new \Twig_SimpleFilter(
                'media_supports', [$this, 'mediaSupports']
            ),
        ];
    }

    /**
     * @param \Twig_Environment $environment
     * @param MediaInterface $media
     * @param $width
     * @param $height
     * @param array $parameters
     * @param null $routeName
     * @param bool $bustCache
     * @return string
     */
    public function media(
        \Twig_Environment $environment,
        MediaInterface $media,
        $width,
        $height,
        array $parameters = [],
        $routeName = null,
        $bustCache = false
    ) {
        $provider = $this->getProviderByMedia($media);

        if ($provider->supportsEmbed()) {
            return $this->mediaEmbed($environment, $media, $width, $height, $parameters);
        }

        return $this->mediaImage($environment, $media, $width, $height, $parameters, $routeName, $bustCache);
    }

    /**
     * @param \Twig_Environment $environment
     * @param MediaInterface $media
     * @param $width
     * @param $height
     * @param array $parameters
     * @return string
     */
    public function mediaEmbed(
        \Twig_Environment $environment,
        MediaInterface $media,
        $width,
        $height,
        array $parameters = []
    ) {
        return $environment->render(
            $this->getProviderByMedia($media)->getEmbedTemplate(),
            [
                'media'      => $media,
                'width'      => $width,
                'height'     => $height,
                'parameters' => $parameters,
            ]
        );
    }

    /**
     * @param \Twig_Environment $environment
     * @param MediaInterface $media
     * @param $width
     * @param $height
     * @param array $extra
     * @param null $routeName
     * @param bool $bustCache
     * @return string
     */
    public function mediaImage(
        \Twig_Environment $environment,
        MediaInterface $media,
        $width,
        $height,
        array $extra = [],
        $routeName = null,
        $bustCache = false
    ) {

        if ($bustCache) {
            $extra['bc'] = time();
        }
        $src = $this->urlGenerator->generate($media->getId(), $width, $height, $extra, $routeName);

        return $environment->render(
            'MediaMonksSonataMediaBundle:Image:image.html.twig',
            [
                'src'    => $src,
                'width'  => $width,
                'height' => $height,
                'title'  => $media->getTitle(),
            ]
        );
    }

    /**
     * @param \Twig_Environment $environment
     * @param MediaInterface $media
     * @param $width
     * @param $height
     * @param array $parameters
     * @param null $routeName
     * @return string
     */
    public function mediaDownload(
        \Twig_Environment $environment,
        MediaInterface $media,
        $width,
        $height,
        array $parameters = [],
        $routeName = null
    ) {
        return $environment->render(
            'MediaMonksSonataMediaBundle:Image:file.html.twig',
            [
                'src'    => $this->urlGenerator->generate($media->getId(), $width, $height, $parameters, $routeName),
                'width'  => $width,
                'height' => $height,
                'title'  => $media->getTitle(),
            ]
        );
    }

    /**
     * @param MediaInterface $media
     * @param $type
     * @return mixed
     */
    public function mediaSupports(MediaInterface $media, $type)
    {
        return $this->getProviderByMedia($media)->supports($type);
    }

    /**
     * @param MediaInterface $media
     * @return ProviderInterface
     */
    private function getProviderByMedia(MediaInterface $media)
    {
        return $this->providerPool->getProvider($media->getProvider());
    }
}

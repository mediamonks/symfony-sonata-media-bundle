<?php

namespace MediaMonks\SonataMediaBundle\Twig\Extension;

use MediaMonks\SonataMediaBundle\Generator\UrlGenerator;
use MediaMonks\SonataMediaBundle\ParameterBag\DownloadParameterBag;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use MediaMonks\SonataMediaBundle\Provider\AbstractProvider;
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
    private $imageUrlGenerator;

    /**
     * @var UrlGenerator
     */
    private $downloadUrlGenerator;

    /**
     * @param ProviderPool $providerPool
     * @param UrlGenerator $imageUrlGenerator
     * @param UrlGenerator $downloadUrlGenerator
     */
    public function __construct(
        ProviderPool $providerPool,
        UrlGenerator $imageUrlGenerator,
        UrlGenerator $downloadUrlGenerator
    ) {
        $this->providerPool = $providerPool;
        $this->imageUrlGenerator = $imageUrlGenerator;
        $this->downloadUrlGenerator = $downloadUrlGenerator;
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
                'media_download_url', [$this, 'mediaDownloadUrl']
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
     * @param array $extra
     * @param null $routeName
     * @param bool $bustCache
     * @return string
     */
    public function media(
        \Twig_Environment $environment,
        MediaInterface $media,
        $width,
        $height,
        array $extra = [],
        $routeName = null,
        $bustCache = false
    ) {
        if ($this->mediaSupports($media, AbstractProvider::SUPPORT_EMBED)) {
            return $this->mediaEmbed($environment, $media, $width, $height, $extra);
        }

        return $this->mediaImage($environment, $media, $width, $height, $extra, $routeName, $bustCache);
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
    public function mediaEmbed(
        \Twig_Environment $environment,
        MediaInterface $media,
        $width,
        $height,
        array $extra = [],
        $routeName = null,
        $bustCache = false
    ) {
        if (!$this->mediaSupports($media, AbstractProvider::SUPPORT_EMBED)) {
            return $this->mediaImage($environment, $media, $width, $height, $extra, $routeName, $bustCache);
        }

        return $environment->render(
            $this->getProviderByMedia($media)->getEmbedTemplate(),
            [
                'media'      => $media,
                'width'      => $width,
                'height'     => $height,
                'parameters' => $extra,
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

        $src = $this->imageUrlGenerator->generate($media, new ImageParameterBag($width, $height, $extra), $routeName);

        return $environment->render(
            'MediaMonksSonataMediaBundle:Media:image.html.twig',
            [
                'media'  => $media,
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
     * @param array $extra
     * @param null $routeNameImage
     * @param null $routeNameDownload
     * @return string
     */
    public function mediaDownload(
        \Twig_Environment $environment,
        MediaInterface $media,
        $width,
        $height,
        array $extra = [],
        $routeNameImage = null,
        $routeNameDownload = null
    ) {
        return $environment->render(
            'MediaMonksSonataMediaBundle:Media:file.html.twig',
            [
                'media'       => $media,
                'downloadSrc' => $this->downloadUrlGenerator->generate(
                    $media,
                    new DownloadParameterBag(),
                    $routeNameDownload
                ),
                'src'         => $this->imageUrlGenerator->generate(
                    $media,
                    new ImageParameterBag($width, $height, $extra),
                    $routeNameImage
                ),
                'width'       => $width,
                'height'      => $height,
                'title'       => $media->getTitle(),
            ]
        );
    }

    /**
     * @param MediaInterface $media
     * @param null $routeName
     * @param array $extra
     * @return string
     */
    public function mediaDownloadUrl(MediaInterface $media, $routeName = null, array $extra = [])
    {
        return $this->downloadUrlGenerator->generate($media, new DownloadParameterBag($extra), $routeName);
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

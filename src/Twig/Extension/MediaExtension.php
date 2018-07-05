<?php

namespace MediaMonks\SonataMediaBundle\Twig\Extension;

use MediaMonks\SonataMediaBundle\Generator\DownloadUrlGenerator;
use MediaMonks\SonataMediaBundle\Generator\ImageUrlGenerator;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use MediaMonks\SonataMediaBundle\Provider\DownloadableProviderInterface;
use MediaMonks\SonataMediaBundle\Provider\EmbeddableProviderInterface;
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
     * @var ImageUrlGenerator
     */
    private $imageUrlGenerator;

    /**
     * @var DownloadUrlGenerator
     */
    private $downloadUrlGenerator;

    /**
     * @param ProviderPool $providerPool
     * @param ImageUrlGenerator $imageUrlGenerator
     * @param DownloadUrlGenerator $downloadUrlGenerator
     */
    public function __construct(
        ProviderPool $providerPool,
        ImageUrlGenerator $imageUrlGenerator,
        DownloadUrlGenerator $downloadUrlGenerator
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
                'media_image_url', [$this, 'mediaImageUrl']
            ),
            new \Twig_SimpleFilter(
                'media_download_url', [$this, 'mediaDownloadUrl']
            )
        ];
    }

    /**
     * @return array
     */
    public function getTests()
    {
        return [
            new \Twig_SimpleTest('media_downloadable', [$this, 'isDownloadable']),
            new \Twig_SimpleTest('media_embeddable', [$this, 'isEmbeddable'])
        ];
    }

    /**
     * @param MediaInterface $media
     * @return bool
     */
    public function isDownloadable(MediaInterface $media)
    {
        return $this->getProviderByMedia($media) instanceof DownloadableProviderInterface;
    }

    /**
     * @param MediaInterface $media
     * @return bool
     */
    public function isEmbeddable(MediaInterface $media)
    {
        return $this->getProviderByMedia($media) instanceof EmbeddableProviderInterface;
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
        if ($this->isEmbeddable($media)) {
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
        if (!$this->isEmbeddable($media)) {
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
            '@MediaMonksSonataMedia/Media/image.html.twig',
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
     * @param array $extraImage
     * @param array $extraDownload
     * @param null $routeNameImage
     * @param null $routeNameDownload
     * @return string
     */
    public function mediaDownload(
        \Twig_Environment $environment,
        MediaInterface $media,
        $width,
        $height,
        array $extraImage = [],
        array $extraDownload = [],
        $routeNameImage = null,
        $routeNameDownload = null
    ) {
        if (!$this->isDownloadable($media)) {
            return '';
        }

        return $environment->render(
            '@MediaMonksSonataMedia/Media/file.html.twig',
            [
                'media'       => $media,
                'downloadSrc' => $this->downloadUrlGenerator->generateDownloadUrl(
                    $media,
                    $extraDownload,
                    $routeNameDownload
                ),
                'src'         => $this->imageUrlGenerator->generateImageUrl(
                    $media,
                    $width, $height, $extraImage,
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
     * @param int $width
     * @param int $height
     * @param array $extra
     * @param null $routeName
     * @return string
     */
    public function mediaImageUrl(MediaInterface $media, $width, $height, array $extra = [], $routeName = null)
    {
        return $this->imageUrlGenerator->generateImageUrl($media, $width, $height, $extra, $routeName);
    }

    /**
     * @param MediaInterface $media
     * @param array $extra
     * @param null $routeName
     * @return string
     */
    public function mediaDownloadUrl(MediaInterface $media, array $extra = [], $routeName = null)
    {
        return $this->downloadUrlGenerator->generateDownloadUrl($media, $extra, $routeName);
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

<?php

namespace MediaMonks\SonataMediaBundle\Twig\Extension;

use MediaMonks\SonataMediaBundle\Generator\DownloadUrlGenerator;
use MediaMonks\SonataMediaBundle\Generator\ImageUrlGenerator;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use MediaMonks\SonataMediaBundle\Provider\DownloadableProviderInterface;
use MediaMonks\SonataMediaBundle\Provider\EmbeddableProviderInterface;
use MediaMonks\SonataMediaBundle\Provider\ProviderInterface;
use MediaMonks\SonataMediaBundle\Provider\ProviderPool;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigTest;

class MediaExtension extends AbstractExtension
{
    private ProviderPool $providerPool;
    private ImageUrlGenerator $imageUrlGenerator;
    private DownloadUrlGenerator $downloadUrlGenerator;

    /**
     * @param ProviderPool $providerPool
     * @param ImageUrlGenerator $imageUrlGenerator
     * @param DownloadUrlGenerator $downloadUrlGenerator
     */
    public function __construct(
        ProviderPool $providerPool,
        ImageUrlGenerator $imageUrlGenerator,
        DownloadUrlGenerator $downloadUrlGenerator
    )
    {
        $this->providerPool = $providerPool;
        $this->imageUrlGenerator = $imageUrlGenerator;
        $this->downloadUrlGenerator = $downloadUrlGenerator;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'media';
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('media', [$this, 'media'], [
                'needs_environment' => true,
                'is_safe' => ['html'],
            ]),
            new TwigFilter('media_embed', [$this, 'mediaEmbed'], [
                'needs_environment' => true,
                'is_safe' => ['html'],
            ]),
            new TwigFilter('media_image', [$this, 'mediaImage'], [
                'needs_environment' => true,
                'is_safe' => ['html'],
            ]),
            new TwigFilter('media_download', [$this, 'mediaDownload'], [
                'needs_environment' => true,
                'is_safe' => ['html'],
            ]),
            new TwigFilter('media_image_url', [$this, 'mediaImageUrl']),
            new TwigFilter('media_download_url', [$this, 'mediaDownloadUrl'])
        ];
    }

    /**
     * @return array
     */
    public function getTests(): array
    {
        return [
            new TwigTest('media_downloadable', [$this, 'isDownloadable']),
            new TwigTest('media_embeddable', [$this, 'isEmbeddable'])
        ];
    }

    /**
     * @param MediaInterface $media
     *
     * @return bool
     */
    public function isDownloadable(MediaInterface $media): bool
    {
        return $this->getProviderByMedia($media) instanceof DownloadableProviderInterface;
    }

    /**
     * @param MediaInterface $media
     *
     * @return bool
     */
    public function isEmbeddable(MediaInterface $media): bool
    {
        return $this->getProviderByMedia($media) instanceof EmbeddableProviderInterface;
    }

    /**
     * @param Environment $environment
     * @param MediaInterface $media
     * @param int|null $width
     * @param int|null $height
     * @param array $extra
     * @param string|null $routeName
     * @param bool $bustCache
     *
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function media(
        Environment $environment,
        MediaInterface $media,
        ?int $width,
        ?int $height,
        array $extra = [],
        ?string $routeName = null,
        bool $bustCache = false
    )
    {
        if ($this->isEmbeddable($media)) {
            return $this->mediaEmbed($environment, $media, $width, $height, $extra);
        }

        return $this->mediaImage($environment, $media, $width, $height, $extra, $routeName, $bustCache);
    }

    /**
     * @param Environment $environment
     * @param MediaInterface $media
     * @param int|null $width
     * @param int|null $height
     * @param array $extra
     * @param string|null $routeName
     * @param bool $bustCache
     *
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function mediaEmbed(
        Environment $environment,
        MediaInterface $media,
        ?int $width,
        ?int $height,
        array $extra = [],
        ?string $routeName = null,
        bool $bustCache = false
    )
    {
        if (!$this->isEmbeddable($media)) {
            return $this->mediaImage($environment, $media, $width, $height, $extra, $routeName, $bustCache);
        }

        return $environment->render(
            $this->getProviderByMedia($media)->getEmbedTemplate(),
            [
                'media' => $media,
                'width' => $width,
                'height' => $height,
                'parameters' => $extra,
            ]
        );
    }

    /**
     * @param Environment $environment
     * @param MediaInterface $media
     * @param int|null $width
     * @param int|null $height
     * @param array $extra
     * @param string|null $routeName
     * @param bool $bustCache
     *
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function mediaImage(
        Environment $environment,
        MediaInterface $media,
        ?int $width,
        ?int $height,
        array $extra = [],
        ?string $routeName = null,
        bool $bustCache = false
    )
    {

        if ($bustCache) {
            $extra['bc'] = time();
        }

        $src = $this->imageUrlGenerator->generate($media, new ImageParameterBag($width, $height, $extra), $routeName);

        return $environment->render(
            '@MediaMonksSonataMedia/Media/image.html.twig',
            [
                'media' => $media,
                'src' => $src,
                'width' => $width,
                'height' => $height,
                'title' => $media->getTitle(),
            ]
        );
    }

    /**
     * @param Environment $environment
     * @param MediaInterface $media
     * @param int|null $width
     * @param int|null $height
     * @param array $extraImage
     * @param array $extraDownload
     * @param string|null $routeNameImage
     * @param string|null $routeNameDownload
     *
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function mediaDownload(
        Environment $environment,
        MediaInterface $media,
        ?int $width,
        ?int $height,
        array $extraImage = [],
        array $extraDownload = [],
        ?string $routeNameImage = null,
        ?string $routeNameDownload = null
    )
    {
        if (!$this->isDownloadable($media)) {
            return '';
        }

        return $environment->render(
            '@MediaMonksSonataMedia/Media/file.html.twig',
            [
                'media' => $media,
                'downloadSrc' => $this->downloadUrlGenerator->generateDownloadUrl(
                    $media,
                    $extraDownload,
                    $routeNameDownload
                ),
                'src' => $this->imageUrlGenerator->generateImageUrl(
                    $media,
                    $width, $height, $extraImage,
                    $routeNameImage
                ),
                'width' => $width,
                'height' => $height,
                'title' => $media->getTitle(),
            ]
        );
    }

    /**
     * @param MediaInterface $media
     * @param int $width
     * @param int $height
     * @param array $extra
     * @param null $routeName
     *
     * @return string
     */
    public function mediaImageUrl(MediaInterface $media, $width, $height, array $extra = [], $routeName = null): string
    {
        return $this->imageUrlGenerator->generateImageUrl($media, $width, $height, $extra, $routeName);
    }

    /**
     * @param MediaInterface $media
     * @param array $extra
     * @param null $routeName
     *
     * @return string
     */
    public function mediaDownloadUrl(MediaInterface $media, array $extra = [], $routeName = null): string
    {
        return $this->downloadUrlGenerator->generateDownloadUrl($media, $extra, $routeName);
    }

    /**
     * @param MediaInterface $media
     *
     * @return ProviderInterface
     */
    private function getProviderByMedia(MediaInterface $media): ProviderInterface
    {
        return $this->providerPool->getProvider($media->getProvider());
    }
}

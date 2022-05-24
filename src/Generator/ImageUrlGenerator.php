<?php

namespace MediaMonks\SonataMediaBundle\Generator;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;

class ImageUrlGenerator extends AbstractUrlGenerator
{
    /**
     * @param MediaInterface $media
     * @param int|null $width
     * @param int|null $height
     * @param array $extra
     * @param string|null $routeName
     * @param int $referenceType
     *
     * @return string
     * @deprecated Use generateImageRedirectUrl instead
     */
    public function generateImageUrl(
        MediaInterface $media,
        ?int $width = null,
        ?int $height = null,
        array $extra = [],
        ?string $routeName = null,
        int $referenceType = SymfonyUrlGeneratorInterface::ABSOLUTE_PATH
    ): string
    {
        if ($routeName === null) {
            $routeName = $this->getRoute(self::ROUTE_IMAGE_REDIRECT);
        }

        return $this->generate($media, new ImageParameterBag($width, $height, $extra), $routeName, $referenceType);
    }

    /**
     * @param MediaInterface $media
     * @param int|null $width
     * @param int|null $height
     * @param array $extra
     * @param int $referenceType
     *
     * @return string
     */
    public function generateImageStreamUrl(
        MediaInterface $media,
        ?int $width = null,
        ?int $height = null,
        array $extra = [],
        int $referenceType = SymfonyUrlGeneratorInterface::ABSOLUTE_PATH
    ): string
    {
        return $this->generate($media, new ImageParameterBag($width, $height, $extra), $this->getRoute(self::ROUTE_IMAGE_STREAM), $referenceType);
    }

    /**
     * @param MediaInterface $media
     * @param int|null $width
     * @param int|null $height
     * @param array $extra
     * @param int $referenceType
     *
     * @return string
     */
    public function generateImageDownloadUrl(
        MediaInterface $media,
        ?int $width = null,
        ?int $height = null,
        array $extra = [],
        int $referenceType = SymfonyUrlGeneratorInterface::ABSOLUTE_PATH
    ): string
    {
        return $this->generate($media, new ImageParameterBag($width, $height, $extra), $this->getRoute(self::ROUTE_IMAGE_DOWNLOAD), $referenceType);
    }

    /**
     * @param MediaInterface $media
     * @param int|null $width
     * @param int|null $height
     * @param array $extra
     * @param int $referenceType
     *
     * @return string
     */
    public function generateImageRedirectUrl(
        MediaInterface $media,
        ?int $width = null,
        ?int $height = null,
        array $extra = [],
        int $referenceType = SymfonyUrlGeneratorInterface::ABSOLUTE_PATH
    ): string
    {
        return $this->generate($media, new ImageParameterBag($width, $height, $extra), $this->getRoute(self::ROUTE_IMAGE_REDIRECT), $referenceType);
    }
}

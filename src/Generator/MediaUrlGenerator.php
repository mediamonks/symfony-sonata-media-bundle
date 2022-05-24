<?php

namespace MediaMonks\SonataMediaBundle\Generator;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\ParameterBag\MediaParameterBag;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;

class MediaUrlGenerator extends AbstractUrlGenerator
{
    /**
     * @param MediaInterface $media
     * @param array $extra
     * @param int $referenceType
     *
     * @return string
     */
    public function generateStreamUrl(
        MediaInterface $media,
        array $extra = [],
        int $referenceType = SymfonyUrlGeneratorInterface::ABSOLUTE_PATH
    ): string
    {
        return $this->generate($media, new MediaParameterBag($extra), $this->getRoute(self::ROUTE_STREAM), $referenceType);
    }

    /**
     * @param MediaInterface $media
     * @param array $extra
     * @param int $referenceType
     *
     * @return string
     */
    public function generateDownloadUrl(
        MediaInterface $media,
        array $extra = [],
        int $referenceType = SymfonyUrlGeneratorInterface::ABSOLUTE_PATH
    ): string
    {
        return $this->generate($media, new MediaParameterBag($extra), $this->getRoute(self::ROUTE_DOWNLOAD), $referenceType);
    }

    /**
     * @param MediaInterface $media
     * @param array $extra
     * @param int $referenceType
     *
     * @return string
     */
    public function generateRedirectUrl(
        MediaInterface $media,
        array $extra = [],
        int $referenceType = SymfonyUrlGeneratorInterface::ABSOLUTE_PATH
    ): string
    {
        return $this->generate($media, new MediaParameterBag($extra), $this->getRoute(self::ROUTE_REDIRECT), $referenceType);
    }
}

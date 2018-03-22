<?php

namespace MediaMonks\SonataMediaBundle\Generator;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;

class ImageUrlGenerator extends AbstractUrlGenerator
{
    /**
     * @param MediaInterface $media
     * @param int $width
     * @param int $height
     * @param array $extra
     * @param null $routeName
     * @param int $referenceType
     * @return string
     */
    public function generateImageUrl(
        MediaInterface $media,
        $width,
        $height,
        array $extra = [],
        $routeName = null,
        $referenceType = SymfonyUrlGeneratorInterface::ABSOLUTE_PATH
    ):string {
        return $this->generate($media, new ImageParameterBag($width, $height, $extra), $routeName, $referenceType);
    }
}

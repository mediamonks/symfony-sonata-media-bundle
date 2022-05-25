<?php

namespace MediaMonks\SonataMediaBundle\Generator;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\ParameterBag\ParameterBagInterface;

interface UrlGeneratorInterface
{
    /**
     * @param MediaInterface $media
     * @param ParameterBagInterface $parameterBag
     * @param string $routeName
     * @param int $referenceType
     *
     * @return string
     */
    public function generate(
        MediaInterface $media,
        ParameterBagInterface $parameterBag,
        string $routeName,
        int $referenceType
    ): string;
}

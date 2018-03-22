<?php

namespace MediaMonks\SonataMediaBundle\Generator;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\ParameterBag\ParameterBagInterface;

interface UrlGeneratorInterface
{

    /**
     * @param MediaInterface $media
     * @param ParameterBagInterface $parameterBag
     * @param $routeName
     * @param $referenceType
     *
     * @return string
     */
    public function generate(
        MediaInterface $media,
        ParameterBagInterface $parameterBag,
        $routeName,
        $referenceType
    ): string;
}

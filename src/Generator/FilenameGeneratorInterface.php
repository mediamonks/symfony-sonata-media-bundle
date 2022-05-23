<?php

namespace MediaMonks\SonataMediaBundle\Generator;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;

interface FilenameGeneratorInterface
{
    public function generate(
        MediaInterface $media,
        ImageParameterBag $parameterBag
    ): string;
}

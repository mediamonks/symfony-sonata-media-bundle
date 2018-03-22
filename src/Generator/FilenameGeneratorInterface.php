<?php

namespace MediaMonks\SonataMediaBundle\Generator;

use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;

interface FilenameGeneratorInterface
{

    public function generate(
        MediaInterface $media,
        ImageParameterBag $parameterBag
    ): string;
}

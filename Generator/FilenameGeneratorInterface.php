<?php

namespace MediaMonks\SonataMediaBundle\Generator;

use MediaMonks\SonataMediaBundle\Handler\ImageParameterBag;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;

interface FilenameGeneratorInterface
{
    public function generate(MediaInterface $media, ImageParameterBag $parameterBag);
}

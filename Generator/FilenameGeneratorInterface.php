<?php

namespace MediaMonks\SonataMediaBundle\Generator;

use MediaMonks\SonataMediaBundle\Handler\ParameterBag;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;

interface FilenameGeneratorInterface
{
    public function generate(MediaInterface $media, ParameterBag $parameterBag);
}

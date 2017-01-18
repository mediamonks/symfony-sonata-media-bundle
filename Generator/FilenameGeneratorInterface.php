<?php

namespace MediaMonks\SonataMediaBundle\Generator;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;

interface FilenameGeneratorInterface
{
    public function generate(MediaInterface $media, array $parameters);
}
